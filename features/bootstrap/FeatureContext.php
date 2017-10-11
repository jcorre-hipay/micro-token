<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Hipay\MicroToken\Model\StorageAdapter\JsonFileStorageAdapter;
use Hipay\MicroToken\Model\KeyStore;

class FeatureContext implements Context
{
    /** @var HttpClient */
    private $httpClient;

    /** @var HttpResponse */
    private $httpResponse;

    /** @var KeyStore */
    private $keyStore;

    public function __construct()
    {
        $this->httpClient = new HttpClient("localhost", 3000);
        $this->httpResponse = null;
    }

    /**
     * @BeforeScenario
     */
    public function before(BeforeScenarioScope $scope)
    {
        $this->keyStore = new KeyStore(new JsonFileStorageAdapter(), "/tmp/micro-token.json");
    }

    /**
     * @AfterScenario
     */
    public function after(AfterScenarioScope $scope)
    {
        if (file_exists("/tmp/micro-token.json")) {
            unlink("/tmp/micro-token.json");
        }
    }

    /**
     * @Given /^the key "([^"]*)" has been registered with identifier (\d+)$/
     */
    public function theKeyHasBeenRegisteredWithIdentifier($value, $identifier)
    {
        $this->keyStore->register($identifier, $value);
    }

    /**
     * @When /^I send the request "POST ([^"]*)" with:$/
     */
    public function iSendTheRequestPostWith($endpoint, TableNode $table)
    {
        $request = [];

        foreach ($table as $row) {
            $request[$row["parameter"]] = $row["value"];
        }

        $this->httpResponse = $this->httpClient->post($endpoint, $request);
    }

    /**
     * @Then /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($statusCode)
    {
        PHPUnit_Framework_TestCase::assertNotNull($this->httpResponse);

        PHPUnit_Framework_TestCase::assertSame(
            intval($statusCode),
            $this->httpResponse->getStatusCode(),
            $this->httpResponse->getContents()
        );
    }

    /**
     * @Then /^the response contents should be:$/
     */
    public function theResponseContentsShouldBe(PyStringNode $response)
    {
        PHPUnit_Framework_TestCase::assertNotNull($this->httpResponse);

        PHPUnit_Framework_TestCase::assertJsonStringEqualsJsonString(
            $response->getRaw(),
            $this->httpResponse->getContents()
        );
    }
}