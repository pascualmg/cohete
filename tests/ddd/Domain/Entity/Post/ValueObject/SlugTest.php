<?php


namespace pascualmg\reactor\ddd\Domain\Entity\Post;

use InvalidArgumentException;
use pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject\Slug;
use PHPUnit\Framework\TestCase;

/**
 * @covers \pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject\Slug
 * @covers \pascualmg\reactor\ddd\Domain\ValueObject\StringValueObject
 */
class SlugTest extends TestCase
{
    public static function isSlug(string $value): bool
    {
        $slugFormatRegex = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
        return preg_match(
            $slugFormatRegex,
            $value
        );
    }
    public function test_given_headline_with_spanish_characters_when_slugified_then_returns_slug(): void
    {
        $this->assertTrue(
            self::isSlug(
            Slug::from(" Esto es un título en Español .")
            )
        );
    }


    public function test_given_title_with_spaces_only_when_slugified_then_invalid_argument_exception(): void
    {
        $this->expectException( InvalidArgumentException::class );
        Slug::from("   ");
    }

    public function test_given_title_with_accents_when_slugified_then_removes_accents(): void
    {
        $this->assertEquals(
            "a-e-i-o-u",
            Slug::from(" á è í ó ú ")
        );
    }

    public function test_given_title_with_uppercase_when_slugified_then_converts_to_lowercase(): void
    {
        $this->assertEquals(
            "this-is-a-title",
            Slug::from("This IS a TitlE")
        );
    }

    public function test_given_title_with_numbers_when_slugified_then_keeps_numbers_in_slug(): void
    {
        $this->assertEquals(
            "title-123",
            Slug::from("title 123")
        );
    }

    public function test_given_empty_title_when_slugified_then_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Slug::from("");
    }

    public function test_given_null_title_when_slugified_then_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Slug::from(null);
    }

    public function test_given_title_with_hyphens_when_slugified_then_removes_extra_hyphens(): void
    {
        $this->assertEquals(
            "this-is-a-title",
            Slug::from("This---is--a-title")
        );
    }

    public function test_given_title_with_multiple_spaces_when_slugified_then_replaces_with_single_hyphens(): void
    {
        $this->assertEquals(
            "this-is-a-title",
            Slug::from("This    is  a   title")
        );
    }

    public function test_given_title_with_special_chars_when_slugified_then_replaces_by_hyphen_and_removes_ampersand(): void
    {
        $this->assertEquals(
            "my-pets-names-are-tom-and-jerry",
            (string)Slug::from(" My Pet's Names Are Tom & Jerry ")
        );
    }
}
