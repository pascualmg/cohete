<?php

namespace pascualmg\reactor\ddd\Domain\ValueObject;

use \Stringable;

class StringValueObject implements Stringable
{
    protected function __construct(public readonly string $value)
    {
    }

    public static function from(?string $value = null) : static
    {
        return new static($value ?? "");
    }

    public function isEmpty() : bool
    {
        return $this->value === "";
    }


    public function __toString(): string
    {
        return $this->value;
    }

    public static function assertMaxLength(int $maxLength, string $value): void
    {
        $currLength = strlen($value > $maxLength);

        if ($currLength > $maxLength) {
            $valueObjectName = self::class;
            throw new \InvalidArgumentException(
                sprintf(
                    "Max length %s exceeded in the %s",
                    $currLength,
                    $valueObjectName
                )
            );
        }
    }

    public static function assertNotNull(?string $value): void
    {
        if ($value === null) {
            $valueObjectName = self::class;
            throw new \InvalidArgumentException(
                sprintf(
                    "Value cannot be null in the %s",
                    $valueObjectName
                )
            );
        }
    }

    public static  function assertNotEmpty(?string $value): void
    {
        if (empty($value)) {
            $valueObjectName = self::class;
            throw new \InvalidArgumentException(
                sprintf(
                    "Value cannot be empty in the %s",
                    $valueObjectName
                )
            );
        }
    }
}