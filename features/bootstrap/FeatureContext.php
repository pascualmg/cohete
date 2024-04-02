<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client as HttpClient;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\PSR11\ContainerFactory;
use Phinx\Migration\Manager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected ContainerInterface $container;
    private HttpClient $client;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->container = ContainerFactory::create();
    }

    /**
     * @Given /^I am an API client$/
     */
    public function iAmAnAPIClient(): void
    {
        $this->client = new HttpClient();
    }

    /**
     * @Given /^the database is empty$/
     */
    public function theDatabaseIsEmpty()
    {
        $configPath = dirname(__DIR__, 2) . '/phinx.php';
        $phinxConfigArray = include($configPath);
        $phinxConfig = new Phinx\Config\Config($phinxConfigArray, $configPath);

        $manager = new Manager($phinxConfig, new StringInput(' '), new NullOutput()); //use ConsoleOutput 4 debug

        $environment = $manager->getEnvironment('development');

        $manager->rollback($environment->getName(), 'all');
        $manager->migrate($environment->getName());
    }

    /**
     * @When /^I request "([^"]*)" with method GET$/
     */
    public function iRequestWithMethodGET($arg1)
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Then /^the response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($arg1)
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Given /^the response should be JSON$/
     */
    public function theResponseShouldBeJSON()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Given /^the response should be an empty Json Array$/
     */
    public function theResponseShouldBeAnEmptyJsonArray()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }
}
