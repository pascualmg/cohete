<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Service\PostCreatorTest;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;

class CreatePostCommandHandlerTest extends PostCreatorTest
{
    private CreatePostCommandHandler $createPostCommandHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createPostCommandHandler = new CreatePostCommandHandler(
            $this->postCreator
        );
    }

    /**
     *
     * @covers \pascualmg\cohete\ddd\Application\Post\CreatePostCommandHandler
     * @covers \pascualmg\cohete\ddd\Application\Post\CreatePostCommand
     * @covers \pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject
     */
    public function test_given_valid_command_when_create_then_service_is_invoked() : void
    {
        $createPostCommand = new CreatePostCommand(
            (string)UuidValueObject::v4(),
            "headline",
            "articlebody",
            "me",
            "2024-04-01T21:46:50+00:00",
        );

        ($this->createPostCommandHandler)(
         $createPostCommand
        );


    }


}
