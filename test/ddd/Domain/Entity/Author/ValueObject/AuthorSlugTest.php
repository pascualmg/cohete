<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorSlug
 * @covers \pascualmg\cohete\ddd\Domain\Service\Slugifier
 * @covers \Cohete\DDD\ValueObject\StringValueObject
 */
class AuthorSlugTest extends TestCase
{
    public function test_given_multi_word_author_name_when_slugified_then_uses_the_full_name(): void
    {
        $this->assertEquals(
            'ambrosio-del-santuario',
            (string) AuthorSlug::from('Ambrosio del Santuario')
        );
    }

    public function test_given_single_word_author_name_when_slugified_then_matches_the_whole_name(): void
    {
        $this->assertEquals(
            'ambrosio',
            (string) AuthorSlug::from('Ambrosio')
        );
    }

    public function test_a_multi_word_author_slug_is_not_the_same_as_its_first_word(): void
    {
        $this->assertFalse(
            AuthorSlug::from('Ambrosio del Santuario')->equals(AuthorSlug::from('Ambrosio'))
        );
    }

    public function test_given_an_already_slugified_url_param_when_reslugified_then_stays_the_same(): void
    {
        // El controller de /blog/{author}/{slug} recibe el slug ya normalizado
        // desde la URL: slugificarlo otra vez debe ser una operacion idempotente.
        $this->assertEquals(
            'ambrosio-del-santuario',
            (string) AuthorSlug::from('ambrosio-del-santuario')
        );
    }

    public function test_given_name_with_accents_when_slugified_then_removes_accents(): void
    {
        $this->assertEquals(
            'jose-maria',
            (string) AuthorSlug::from('José María')
        );
    }

    public function test_given_author_name_value_object_when_from_name_then_returns_same_slug_as_from_string(): void
    {
        $this->assertTrue(
            AuthorSlug::fromName(AuthorName::from('Ambrosio del Santuario'))
                ->equals(AuthorSlug::from('Ambrosio del Santuario'))
        );
    }

    public function test_given_empty_name_when_slugified_then_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AuthorSlug::from('');
    }

    public function test_given_null_name_when_slugified_then_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AuthorSlug::from(null);
    }
}
