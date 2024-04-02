<?php

use Behat\Behat\Context\Context;
use GuzzleHttp\Client as HttpClient;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Infrastructure\PSR11\ContainerFactory;
use Phinx\Migration\Manager as PhinxManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected ContainerInterface $container;
    private HttpClient $client;
    protected ResponseInterface $response;
    private const string PHINX_ENVIRONMENT = 'development';
    private PhinxManager $phinxManager;


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
        $this->phinxManager = $this->getPhinxManager();
    }

    /**
     * @beforeSuite
     */
    public static function beforeSuite(\Behat\Testwork\Hook\Scope\BeforeSuiteScope $scope){

       echo 'holi' ;
    }

    /**
     * @Given /^I am an API client$/
     */
    public function iAmAnAPIClient(): void
    {
        $this->client = new HttpClient(
            [
                'base_uri' => 'http://localhost:8000'
            ]
        );
    }

    /**
     * @Given /^the database is empty$/
     */
    public function theDatabaseIsEmpty()
    {
        $this->phinxManager->rollback(self::PHINX_ENVIRONMENT, 'all');
        $this->phinxManager->migrate(self::PHINX_ENVIRONMENT);
    }


    private function getPhinxManager() : PhinxManager
    {
        $configPath = dirname(__DIR__, 2) . '/phinx.php';
        $phinxConfigArray = include($configPath);
        $phinxConfig = new Phinx\Config\Config($phinxConfigArray, $configPath);
        return new PhinxManager($phinxConfig, new StringInput(' '), new NullOutput()); //use ConsoleOutput 4 debug
    }


    /**
     * @Then /^the response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($expetedCode)
    {
        $actualCode = $this->response->getStatusCode();
        if($actualCode !== (int)$expetedCode) {
            throw new \Exception("Expected response code $expetedCode, but received $actualCode");
        }
    }



    /**
     * @Given /^the response should be an empty Json Array$/
     */
    public function theResponseShouldBeAnEmptyJsonArray()
    {
        $contents = $this->response->getBody()->getContents();
        $decodedArray = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($decodedArray) || !empty($decodedArray)) {
                throw new \Exception("Expected an empty JSON array, but received: " . $contents);
            }
    }

    /**
     * @When /^I request "([^"]*)" with method "([^"]*)"$/
     */
    public function iRequestWithMethod($url, $method): void
    {
        $this->response = [$this->client, $method]($url);
    }

    /**
     * @Given /^the database has fixtures$/
     */
    public function theDatabaseHasFixtures(): void
    {
        $this->phinxManager->rollback(self::PHINX_ENVIRONMENT);
        $this->phinxManager->migrate(self::PHINX_ENVIRONMENT);
        $this->phinxManager->seed(self::PHINX_ENVIRONMENT);
    }

    /**
     * @Given /^The response Items are Posts$/
     */
    public function theResponseItemsArePosts()
    {
        $items = json_decode(
            $this->response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $item = $items[0];

        $keys = array_keys($item);

        if(["id", "headline", "slug", "articleBody", "author", "datePublished"]  !== $keys){
            throw new Exception('missing or new key not tested added to post serialization');
        }

    }


}
