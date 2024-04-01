<?php

namespace test\pascualmg\reactor\ddd\Application\Post;

use pascualmg\reactor\ddd\Application\Post\CreatePostCommand;
use pascualmg\reactor\ddd\Application\Post\CreatePostCommandHandler;
use pascualmg\reactor\ddd\Domain\ValueObject\UuidValueObject;
use test\pascualmg\reactor\ddd\Domain\Service\PostCreatorTest;

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
     * @covers \pascualmg\reactor\ddd\Application\Post\CreatePostCommandHandler
     * @covers \pascualmg\reactor\ddd\Application\Post\CreatePostCommand
     * @covers \pascualmg\reactor\ddd\Domain\ValueObject\UuidValueObject
     */
    public function test_given_valid_command_when_create_then_service_is_invoked() : void
    {
        $createPostCommand = new CreatePostCommand(
            (string)UuidValueObject::v4(),
            "headline",
            "articlebody",
            "me",
            "2024-04-01T18:20:09+00f",
        );

        ($this->createPostCommandHandler)(
         $createPostCommand
        );


    }


}
