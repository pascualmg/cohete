<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{

    /**
     * @covers Post::jsonSerialize
     */
    public function test_given_a_post_when_serialize_then_i_got_this_serializable_array(): void
    {
        $post = PostMother::randomValid();
        $serializableArray = array(
            'id' => $post->id->value,
            'headline' => $post->headline->value,
            'slug' => $post->slug->value,
            'articleBody' => $post->articleBody->value,
            'author' => $post->author->value,
            'datePublished' => $post->datePublished->value,
        );

        $this->assertEquals(
            $serializableArray,
            $post->jsonSerialize()
        );
    }

}
