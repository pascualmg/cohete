<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Service\PostCreator;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;
use PHPUnit\Framework\TestCase;

class CreatePostCommandHandlerTest  extends TestCase
{
    private CreatePostCommandHandler $createPostCommandHandler;
    private PostCreator $postCreator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postCreator = $this->createMock(PostCreator::class);
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

        $this->postCreator->expects($this->once())->method('__invoke');

        ($this->createPostCommandHandler)(
         $createPostCommand
        );


    }


}
