<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Media;

/**
 * AWS Signature V4 firmador local (sin IO, sin async). Compatible con cualquier backend S3 (MinIO, Garage, AWS S3, R2 de Cloudflare...).
 *
 * Implementa unicamente lo que necesitamos: firma de requests HTTP y
 * generacion de presigned URLs. El resto del SigV4 (headers chunked, STS
 * tokens, etc.) NO esta implementado: el dia que haga falta, se anade.
 *
 * Spec oficial:
 *   https://docs.aws.amazon.com/general/latest/gr/signing_aws_api_requests.html
 */
final readonly class Aws4Signer
{
    public function __construct(
        public string $accessKey,
        public string $secretKey,
        public string $region = 'us-east-1',
        public string $service = 's3',
    ) {
    }

    /**
     * Firma un request HTTP. Devuelve los headers que hay que anadir.
     *
     * @param string $method HTTP method (PUT, GET, DELETE, HEAD)
     * @param string $url URL completa con scheme + host + path + query
     * @param array<string,string> $headers headers ya presentes (content-type, etc)
     * @param string $bodyHash sha256 hex del body (UNSIGNED-PAYLOAD si no se quiere firmar body)
     * @return array<string,string> headers finales (incluye Authorization, x-amz-*)
     */
    public function signRequest(
        string $method,
        string $url,
        array $headers = [],
        string $bodyHash = 'UNSIGNED-PAYLOAD',
    ): array {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $amzDate = $now->format('Ymd\THis\Z');
        $dateStamp = $now->format('Ymd');

        $parsed = parse_url($url);
        $host = $parsed['host'] . (isset($parsed['port']) ? ":{$parsed['port']}" : '');
        $canonicalUri = $this->canonicalUri($parsed['path'] ?? '/');
        $canonicalQuery = $this->canonicalQuery($parsed['query'] ?? '');

        // Headers obligatorios
        $headers = array_change_key_case($headers, CASE_LOWER);
        $headers['host'] = $host;
        $headers['x-amz-date'] = $amzDate;
        $headers['x-amz-content-sha256'] = $bodyHash;

        // Canonical headers (ordenados alfabeticamente)
        ksort($headers);
        $canonicalHeaders = '';
        $signedHeadersList = [];
        foreach ($headers as $name => $value) {
            $canonicalHeaders .= $name . ':' . trim((string) $value) . "\n";
            $signedHeadersList[] = $name;
        }
        $signedHeaders = implode(';', $signedHeadersList);

        // Canonical request
        $canonicalRequest = implode("\n", [
            strtoupper($method),
            $canonicalUri,
            $canonicalQuery,
            $canonicalHeaders,
            $signedHeaders,
            $bodyHash,
        ]);

        // String to sign
        $credentialScope = "{$dateStamp}/{$this->region}/{$this->service}/aws4_request";
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $amzDate,
            $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        // Derive signing key
        $kDate    = hash_hmac('sha256', $dateStamp, "AWS4{$this->secretKey}", true);
        $kRegion  = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', $this->service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $headers['authorization'] = "AWS4-HMAC-SHA256 "
            . "Credential={$this->accessKey}/{$credentialScope}, "
            . "SignedHeaders={$signedHeaders}, "
            . "Signature={$signature}";

        return $headers;
    }

    /**
     * Genera URL presigned (firma incluida en query string, no headers).
     */
    public function presignUrl(
        string $method,
        string $url,
        int $expiresSeconds = 3600,
    ): string {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $amzDate = $now->format('Ymd\THis\Z');
        $dateStamp = $now->format('Ymd');

        $parsed = parse_url($url);
        $host = $parsed['host'] . (isset($parsed['port']) ? ":{$parsed['port']}" : '');
        $canonicalUri = $this->canonicalUri($parsed['path'] ?? '/');

        $credentialScope = "{$dateStamp}/{$this->region}/{$this->service}/aws4_request";

        // Query params para presigned (alfabetico)
        $params = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
        }
        $params['X-Amz-Algorithm'] = 'AWS4-HMAC-SHA256';
        $params['X-Amz-Credential'] = "{$this->accessKey}/{$credentialScope}";
        $params['X-Amz-Date'] = $amzDate;
        $params['X-Amz-Expires'] = (string) $expiresSeconds;
        $params['X-Amz-SignedHeaders'] = 'host';
        ksort($params);

        $canonicalQuery = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $canonicalHeaders = "host:{$host}\n";
        $signedHeaders = 'host';

        $canonicalRequest = implode("\n", [
            strtoupper($method),
            $canonicalUri,
            $canonicalQuery,
            $canonicalHeaders,
            $signedHeaders,
            'UNSIGNED-PAYLOAD',
        ]);

        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $amzDate,
            $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        $kDate    = hash_hmac('sha256', $dateStamp, "AWS4{$this->secretKey}", true);
        $kRegion  = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', $this->service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        $signature = hash_hmac('sha256', $stringToSign, $kSigning);
        $params['X-Amz-Signature'] = $signature;

        return $parsed['scheme'] . '://' . $host . $parsed['path']
            . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    private function canonicalUri(string $path): string
    {
        $segments = array_map('rawurlencode', explode('/', $path));
        return implode('/', $segments);
    }

    private function canonicalQuery(string $query): string
    {
        if ($query === '') {
            return '';
        }
        parse_str($query, $params);
        ksort($params);
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
