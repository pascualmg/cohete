<?php

namespace pascualmg\reactor\ddd\Domain\ValueObject;

class AtomDateValueObject extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        parent::assertNotNull($value);
        parent::assertNotEmpty($value);

        self::assertHasCorrectDatetimeInterfaceAtomFormat($value);
        return parent::from(
            $value
        );
    }
    private static function assertHasCorrectDatetimeInterfaceAtomFormat(string $value): void
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(.\d+)?(Z|([+\-])\d{2}:\d{2})$/', $value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid Atom date format, used %s  , example : %s",
                    $value,
                    (new \DateTimeImmutable('now'))->format(\DateTimeInterface::ATOM)
                )
            );
        }
    }

    public function getDatetimeImmutable(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $this->value);
    }
}
