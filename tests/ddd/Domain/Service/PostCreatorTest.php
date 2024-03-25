<?php

namespace ddd\Domain\Service;

use pascualmg\reactor\ddd\Domain\Bus\Message;
use pascualmg\reactor\ddd\Domain\Bus\MessageBus;
use pascualmg\reactor\ddd\Domain\Entity\Post\Post;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use pascualmg\reactor\ddd\Domain\Service\PostCreator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\Promise\Deferred;

/** @covers \pascualmg\reactor\ddd\Domain\Service\PostCreator
 * @covers  \pascualmg\reactor\ddd\Domain\Bus\Message
 * @covers  \pascualmg\reactor\ddd\Domain\Entity\Post\Post
 * @covers  \pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject\ArticleBody
 * @covers  \pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject\Author
 * @covers  \pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject\HeadLine
 * @covers  \pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject\Slug
 * @covers  \pascualmg\reactor\ddd\Domain\ValueObject\AtomDateValueObject
 * @covers  \pascualmg\reactor\ddd\Domain\ValueObject\StringValueObject
 * @covers  \pascualmg\reactor\ddd\Domain\ValueObject\UuidValueObject
 */
class PostCreatorTest extends TestCase
{
    private PostCreator $postCreator;
    private PostRepository $postRepository;
    private LoggerInterface $logger;


    protected function setUp(): void
    {
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->messageBus = $this->createMock(MessageBus::class);
        $this->logger = $this->createMock(LoggerInterface::class);


        $this->postCreator = new PostCreator(
            $this->postRepository,
            $this->messageBus,
            $this->logger
        );
    }

    public function test_given_a_post_when_create_then_is_created_and_event_is_dispatched(): void
    {
        $postToCreate = Post::fromPrimitives(
            "be0f19bf-5225-4a9d-8cd9-0a8735d20aa6",
            "some title",
            "this is the articlebody",
            "me",
            "2024-03-11T12:25:51+00:00",
        );

        $deferred = new Deferred();
        $deferred->resolve(false);
        $this->postRepository->method('save')->willReturn(
            $deferred->promise()
        );

        $this->messageBus->expects($this->once())
            ->method('publish')
            ->with(
                new Message('domain_event.post_created', [$postToCreate])
            );

        ($this->postCreator)(
            (string)$postToCreate->id,
            (string)$postToCreate->headline,
            (string)$postToCreate->articleBody,
            (string)$postToCreate->author,
            (string)$postToCreate->datePublished
        );
    }

}
