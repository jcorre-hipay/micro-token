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
        $this->keyStore = new KeyStore(new JsonFileStorageAdapter(), "/tmp/micro-token.json");
    }

    /**
     * @Given /^the key "([^"]*)" has been registered with identifier (\d+)$/
     */
    public function theKeyHasBeenRegisteredWithIdentifier($value, $identifier)
    {
        throw new PendingException();
    }

    /**
     * @When /^I send the request "POST ([^"]*)" with:$/
     */
    public function iSendTheRequestPostWith($endpoint, TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Then /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($statusCode)
    {
        throw new PendingException();
    }

    /**
     * @Then /^the response contents should be:$/
     */
    public function theResponseContentsShouldBe(PyStringNode $response)
    {
        throw new PendingException();
    }
}