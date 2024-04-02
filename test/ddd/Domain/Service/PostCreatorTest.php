<?php

namespace pascualmg\cohete\ddd\Domain\Service;

use pascualmg\cohete\ddd\Domain\Bus\Message;
use pascualmg\cohete\ddd\Domain\Bus\MessageBus;
use pascualmg\cohete\ddd\Domain\Entity\Post\PostMother;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\Promise\Deferred;

/** @covers \pascualmg\cohete\ddd\Domain\Service\PostCreator
 * @covers  \pascualmg\cohete\ddd\Domain\Bus\Message
 * @covers  \pascualmg\cohete\ddd\Domain\Entity\Post\Post
 * @covers  \pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\ArticleBody
 * @covers  \pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Author
 * @covers  \pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\HeadLine
 * @covers  \pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug
 * @covers  \pascualmg\cohete\ddd\Domain\ValueObject\AtomDateValueObject
 * @covers  \pascualmg\cohete\ddd\Domain\ValueObject\StringValueObject
 * @covers  \pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject
 */
class PostCreatorTest extends TestCase
{
    protected PostCreator $postCreator;
    protected PostRepository $postRepository;
    protected LoggerInterface $logger;

    public function __construct(string $name)
    {
        parent::__construct($name);
    }


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
        $postToCreate = PostMother::randomValid();

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
    public function test_given_a_post_when_create_with_error_then_this_concrete_error_is_logged(): void
    {
        $postToCreate = PostMother::randomValid();

        $concreteException = new class extends \Exception
        {
            public function __construct()
            {
                parent::__construct('some error message', 0, null);
            }

        };

        $deferred = new Deferred();
        $deferred->reject($concreteException);
        $this->postRepository->method('save')->willReturn(
            $deferred->promise()
        );

        $this->logger->expects($this->once())
                ->method('error')
                ->with(
                    'Cant create the new post',
                    [$postToCreate, $concreteException]
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
