# Behat

## What is it?

Behat is a framework for testing an application against user stories.

## How does it work?

All Behat stuff is located into the `features` directory at the root of the project.
This directory contains a list of _feature_ files and another directory named
`bootstrap`.

The _feature_ files are the user stories describing the application. Each file
consists in a list of _scenarios_, each scenario consisting in a list of _steps_.

For an example, a scenario could be:

    Scenario: Listing files in a directory
        
        Given I am in a directory "test"
        And I have a file named "foo"
        And I have a file named "bar"
        When I run "ls"
        Then I should get "foo bar"
        
Each scenario is a functional test. When Behat is run, it will test each scenario
independently from each other.

On the other side, the `bootstrap` directory contains the _implementation_ of
the tests. It contains a list of PHP classes, which can be loaded by Behat at runtime.

In the example above, the scenario contains 5 steps. For each step, Behat will
search for the corresponding implementation in a _context_, usually a class named
`FeatureContext`. The matching between the step and the method to execute is based
on regular expressions:

    class FeatureContext implements Context
    {
        /**
         * @Given /^I am in a directory "([^"]*)"$/
         */
        public function iAmInADirectory($directory)
        {
            // code to execute on first step
        }
        ...
    }

If the method throws an exception, the step fails and the scenario is stopped.
Otherwise, the step succeeds, and the next one is executed. The scenario succeeds
if all steps succeeded.

## How to write a scenario?

A scenario is a list of steps. Each step must begin with one of the keywords:
`Given`, `When`, `Then`, `And` or `But`.

A scenario must follow the pattern:

    Given <precondition>
    When <action>
    Then <result>
    
The keywords `And` and `But` are used to add _preconditions_, _actions_ and
_results_. Preconditions are optional.

## Try it!

Switch to the branch `course-behat` and read the feature `create_token.feature`.
Three scenarios have to be implemented. If you take a look to the file
`bootstrap/FeatureContext.php`, you will see that the steps are present but empty.

You can run the tests with:

    $ bin/behat
    
### Implementing the steps

Try to implement the steps to pass the first scenario.

You can see two data structures provided by Behat: `TableNode` and `PyStringNode`.

The first one can be iterated in a `foreach` loop as a collection of associative
arrays. The keys are defined by the first line of the table, here `parameter` and
`value`.

The second one has a method `PyStringNode::getRow()` to get the contents of the string
between the `"""` delimiters.

Assertions are not provided by Behat itself, you can use the class
`PHPUnit_Framework_TestCase` instead.

**Solution below**

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
    
The first scenario passed successfully, but wait, the others failed...

Why? Because the data stored by the `KeyStore` are persistent but not cleared
between each test.

### Hooking into the test process

Behat provides a lot of hooks to execute code at some points during the test process.

What we will see here is the `@BeforeScenario` and the `@AfterScenario` hooks, which
can be annotated to a method to execute it respectively before and after each scenario.

Add the following method to the `FeatureContext` class:

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

By the way, the instantiation of the `KeyStore` can be removed from the constructor.

All the tests are green now!

### Refactoring the scenarios with backgrounds

The three scenarios begin with the same step:

    Given the key "l1br4ry" has been registered with identifier 4
    
Behat provides a way to avoid duplication by the `Background` keyword.
The steps under `Background` are executed before each scenario, but after the
`@BeforeScenario` hook.

Remove the step above from the three scenarios and add the following before the
first one:

    Background:
        
        Given the key "l1br4ry" has been registered with identifier 4

If you run the tests, they are still green!

### Refactoring the scenarios with scenario outlines

If we look closer at our scenarios, we can see they follow the same flow,
but with different values.

Behat provides the `Scenario Outline` keyword for this situation. It is used to
define a _scenario template_ with _placeholders_, and then run the template with
different sets of values injected into the placeholders.

Replace the three scenarios by the following to get the exact same tests, but
written more concisely.

    Scenario Outline: Creating a token
        
        Given the key "l1br4ry" has been registered with identifier 4
        When I send the request "POST /token" with:
            | parameter      | value           |
            | card_number    | 372999410121001 |
            | algorithm      | <algorithm>     |
            | key_identifier | 4               |
        
        Then the response status code should be 201
        And the response contents should be:
            """
            {
                "code": 201,
                "token": "<token>"
            }
            """
        
        Examples:
            | algorithm | token                                                            |
            | md5       | 5d4122f7fcbbf9d3738176596160a741                                 |
            | sha1      | 62f6446c839e3749a938fa7d468c79f5d247c3c2                         |
            | sha256    | c061628b32afd532463daf2b771cb7306cbbfea3857bcd21f1785c7eed1efb54 |
