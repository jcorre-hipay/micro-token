# PhpUnit

## What is it?

PhpUnit is a programmer-oriented testing framework. It can be used to write any
type of test: unit, integration, functional or whatever.

## How does it work?

All PhpUnit tests are located into the `tests` directory at the root of the project.

A PhpUnit test is a class suffixed by `Test` and inheriting the
`\PHPUnit_Framework_TestCase` class. Each method beginning by `test` or annotated
with `@test` is a test describing one particular behaviour.

## How to use it?

PhpUnit provides a lot of features and is really permissive on how to write a test.
But this has a down side: it is easy to write complex and unreadable tests. So keep
focus on readability.

Since Behat is used for functional tests and PhpSpec is used for unit tests, PhpUnit
covers here integration tests.

## Try it!

Switch to the branch `course-phpunit` and open the file `tests/TokenTest.php`. The
application has only one module, and this file is where its integration tests will
take place.

### First test

Start with the method `testTokenCreationSuccessfully`. This test should validate
that a token can be created successfully with a client code like:

    $keyStore->register(4, "l1br4ry");
    $tokenManager->setCipher($cipherRegistry->get("md5"));
    $token = $this->tokenManager->create("372999410121001", 4);
    // $token === "5d4122f7fcbbf9d3738176596160a741"
    
#### Initialization of the module

The first step is to create the objects and assemble them to build the module.

    $ciphers = [
        "md5" => new Md5Cipher(),
        "sha1" => new Sha1Cipher(),
        "sha256" => new Sha256Cipher(),
    ];

    $cipherRegistry = new CipherRegistry($ciphers);
    $keyStore = new KeyStore(new JsonFileStorageAdapter(), "/tmp/micro-token.json");
    $tokenManager = new TokenManager(new Logger("test"), $keyStore);

#### Action!

We have a working module, so it is time to use it to produce a token.

    $keyStore->register(4, "l1br4ry");
    $tokenManager->setCipher($cipherRegistry->get("md5"));
    $token = $this->tokenManager->create("372999410121001", 4);

#### Verifications

Now, the last step: validate that the output of the module is the same as expected.
PhpUnit provides a lot of methods of the form `assert<something>` to compare things
in various ways. If the comparison fails, an exception is thrown and the test fails
(unless you catch the exception).

    static::assertSame("5d4122f7fcbbf9d3738176596160a741", $token);

Run the test with:

    bin/phpunit

It passed!

### Hooking the test process

Okay, the test passed... once. The second time you run the test, it fails. Why?
Because the keys are persisted on the file system and can't be registered twice.
The data file should be removed after the test.

But wait a second. More tests will be added, and the "clean up" step will probably
be needed after each test. Furthermore, each test will begin by the initialization of
the module, which will be exactly the same every time.

PhpUnit provides the solution with two methods, `setUp` and `tearDown`, which are
called respectively _before_ and _after_ each test. Great! Let's use them.

    /** @var CipherRegistry */
    private $cipherRegistry;

    /** @var KeyStore */
    private $keyStore;

    /** @var TokenManager */
    private $tokenManager;

    protected function setUp()
    {
        $ciphers = [
            "md5" => new Md5Cipher(),
            "sha1" => new Sha1Cipher(),
            "sha256" => new Sha256Cipher(),
        ];

        $this->cipherRegistry = new CipherRegistry($ciphers);
        $this->keyStore = new KeyStore(new JsonFileStorageAdapter(), "/tmp/micro-token.json");
        $this->tokenManager = new TokenManager(new Logger("test"), $this->keyStore);
    }

    protected function tearDown()
    {
        if (file_exists("/tmp/micro-token.json")) {
            unlink("/tmp/micro-token.json");
        }
    }

    public function testTokenCreationSuccessfully()
    {
        $this->keyStore->register(4, "l1br4ry");
        $this->tokenManager->setCipher($this->cipherRegistry->get("md5"));
        $token = $this->tokenManager->create("372999410121001", 4);

        static::assertSame("5d4122f7fcbbf9d3738176596160a741", $token);
    }
    
Bonus: PhpUnit also provides the static methods `setUpBeforeClass` and
`tearDownAfterClass`, respectively called _before the first test_ and _after the
last one_ of the class.

### Data provider

What if we want to test the same case with sha1 or sha256 algorithm? One solution
is to duplicate the method `testTokenCreationSuccessfully` for each algorithm.

A better solution would be to parametrized the test. Guess what? PhpUnit provides
that feature! A test can be annotated with `@dataProvider <method>`. The method
referenced by the annotation must return a collection of array, each array
representing a set of variables.

    /**
     * @dataProvider provideCipherAlgorithms
     */
    public function testTokenCreationSuccessfully($expectedToken, $algorithm)
    {
        $this->keyStore->register(4, "l1br4ry");
        $this->tokenManager->setCipher($this->cipherRegistry->get($algorithm));
        $actualToken = $this->tokenManager->create("372999410121001", 4);

        static::assertSame($expectedToken, $actualToken);
    }
    
    public function provideCipherAlgorithms()
    {
        return [
            "md5" => ["5d4122f7fcbbf9d3738176596160a741", "md5"],
            "sha1" => ["62f6446c839e3749a938fa7d468c79f5d247c3c2", "sha1"],
            "sha256" => ["c061628b32afd532463daf2b771cb7306cbbfea3857bcd21f1785c7eed1efb54", "sha256"],
        ];
    }

### Mock objects

Next, we want to test our module in total isolation by removing the side-effects caused to the
file system. Remember that data file `/tmp/micro-token.json`? And what about that logger we
created in the `setUp` function?

So we need a way to replace the objects dealing with the file system by fake ones: mock objects!
PhpUnit provides that feature via the method `getMockBuilder(<classname>)`:

    $mock = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();

The resulting `$mock` object provides methods to set some expectations. For example, the following
code sets the expectation that the `load` method should be called exactly one time, with the argument
`"/tmp/micro-token.data"`, and returns the array `[4 => "l1br4ry"]`.

    $mock->expects(static::once())
        ->method("load")
        ->with("/tmp/micro-token.data")
        ->willReturn([4 => "l1br4ry"]);

Let's try! Update the tests by mocking the logger and the storage adapter. By the way, the
`tearDown` method is not needed anymore.

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $storageAdapter;
    
    /** @var CipherRegistry */
    private $cipherRegistry;
    
    /** @var KeyStore */
    private $keyStore;
    
    /** @var TokenManager */
    private $tokenManager;
    
    protected function setUp()
    {
        $this->storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
    
        $ciphers = [
            "md5" => new Md5Cipher(),
            "sha1" => new Sha1Cipher(),
            "sha256" => new Sha256Cipher(),
        ];
    
        $this->cipherRegistry = new CipherRegistry($ciphers);
        $this->keyStore = new KeyStore($this->storageAdapter, "/tmp/micro-token.data");
        $this->tokenManager = new TokenManager(
            $this->getMockBuilder(LoggerInterface::class)->getMock(),
            $this->keyStore
        );
    }
    
    /**
     * @dataProvider provideCipherAlgorithms
     */
    public function testTokenCreationSuccessfully($expectedToken, $algorithm)
    {
        $this->storageAdapter
            ->expects(static::once())
            ->method("save")
            ->with("/tmp/micro-token.data", [4 => "l1br4ry"]);
    
        $this->keyStore->register(4, "l1br4ry");
    
        $this->storageAdapter
            ->expects(static::once())
            ->method("load")
            ->with("/tmp/micro-token.data")
            ->willReturn([4 => "l1br4ry"]);
    
        $this->tokenManager->setCipher($this->cipherRegistry->get($algorithm));
        $actualToken = $this->tokenManager->create("372999410121001", 4);
    
        static::assertSame($expectedToken, $actualToken);
    }
    
    public function provideCipherAlgorithms()
    {
        return [
            "md5" => ["5d4122f7fcbbf9d3738176596160a741", "md5"],
            "sha1" => ["62f6446c839e3749a938fa7d468c79f5d247c3c2", "sha1"],
            "sha256" => ["c061628b32afd532463daf2b771cb7306cbbfea3857bcd21f1785c7eed1efb54", "sha256"],
        ];
    }

### Expecting exceptions

What's happen if `TokenManager::setCipher` is not called? An exception of class `NoCipherException`
is thrown. So let's write a test for it! To expect an exception, you can use the method...
`expectException`!

    public function testFailureWhenNoCiphersSet()
    {
        $this->storageAdapter
            ->expects(static::never())
            ->method("load");

        $this->expectException(NoCipherException::class);

        $this->tokenManager->create("372999410121001", 4);
    }
