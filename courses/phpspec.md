# PhpSpec

## What is it?

PhpSpec is a tool for designing and unit testing an application by the
behaviour-driven-development approach.

## How does it work?

All PhpSpec tests are located into the `spec` directory at the root of the project.
It mirrors the structure of the `src` directory, but each class is replaced by its
unit test.

A PhpSpec test is a class suffixed by `Spec` and inheriting the
`PhpSpec\ObjectBehavior` class. Each method beginning by `it_` or `its_` is a test
describing a particular behaviour of the tested class.

## How to use it?

The important rules for using PhpSpec are:

* write test first to focus on the behaviour and not the implementation
* have readable tests to ease code maintenance
* don't hesitate to redesign your code when a test becomes too complex

The last point is important, because a complex test means that something is wrong
in your code design: perhaps your class has too much responsibilities
or is too much coupled with another one or whatever.

## Try it!

Switch to the branch `course-phpspec` and open the file `src/Model/TokenManager.php`.
This class needs to be implemented, so the first thing to do is: write the test!

### First step

Open the file `spec/Model/TokenManagerSpec.php`.

We have to describe the class `TokenManager` here, so the first question to ask is:
what does this object should do? And the response is: creating a token from a
card number and a key. So, create the first test:

    function it_creates_a_token_from_a_card_number_and_a_key()
    {
    }
    
Note the conventions here:
* don't use `public`, `protected` or `private` before the keyword `function`
* use snake case for the method naming

Now, write the following expectation: calling the `create` method with the card number
`372999410121001` and the key identifier `4` should return `5d4122f7fcbbf9d3738176596160a741`.

With PhpSpec, it gives:

    function it_creates_a_token_from_a_card_number_and_a_key()
    {
        $this->create("372999410121001", 4)->shouldReturn("5d4122f7fcbbf9d3738176596160a741");
    }
    
Some explanations:
* `$this` refers to the object under test, so `$this` has access to all the methods of
`TokenManager`
* a method call can be followed by an expectation of the form `should<something>`

Run the test with:

    bin/phpspec run
    
It's red! Turn it green with a first and dummy implementation:

    public function create($cardNumber, $keyIdentifier)
    {
        return "5d4122f7fcbbf9d3738176596160a741";
    }
    
### Mock objects

A `TokenManager` object uses two other objects to create a token:
* a `KeyStore` object to retrieve a key from its identifier
* a `CipherInterface` object to hash the card number with a key

PhpSpec provides an easy way to create mock objects: passing them as arguments to the test
method. Expectations and return values can be added to the method calls of the mock objects
in a fluent way.

    function it_creates_a_token_from_a_card_number_and_a_key(
        KeyStore $keyStore,
        CipherInterface $cipher
    ) {
        $keyStore->get(4)->shouldBeCalled()->willReturn("l1br4ry");
        
        $cipher->hash("372999410121001", "l1br4ry")->shouldBeCalled()->willReturn("5d4122f7fcbbf9d3738176596160a741");
        
        $this->setCipher($cipher);
        
        $this->create("372999410121001", 4)->shouldReturn("5d4122f7fcbbf9d3738176596160a741");
    }
    
Now, if you run the test, it should tell you that the new expectations are not fulfilled.

Go back to the class and add the code to pass the test:

    class TokenManager
    {   
        private $keyStore;
        private $cipher;
        
        public function __construct(KeyStore $keyStore)
        {
            $this->keyStore = $keyStore;
            $this->cipher = null;
        }
        
        public function setCipher(CipherInterface $cipher)
        {
            $this->cipher = $cipher;
            
            return $this;
        }
        
        public function create($cardNumber, $keyIdentifier)
        {
            return $this->cipher->hash($cardNumber, $this->keyStore->get($keyIdentifier));
        }
    }
    
Still not working? Yes, because we need to pass the `KeyStore` object during the
instantiation of the tested object. To do that, add the following line at the beginning of
the test:

    $this->beConstructedWith($keyStore);
    
The test is now green!
    
### Expecting exceptions

Okay, we have a working class, but what happens if the `create` method is invoked before
any cipher has been set?

In this case we want an exception to be thrown saying "No ciphers have been defined.".
New behaviour, new test:

    function it_throws_an_exception_when_no_ciphers_have_been_set(
        KeyStore $keyStore
    ) {
        $this->beConstructedWith($keyStore);
        
        $this
            ->shouldThrow(new NoCipherException("No ciphers have been defined."))
            ->during("create", ["372999410121001", 4]);
    }

Now, add the behaviour to the class:

    public function create($cardNumber, $keyIdentifier)
    {
        if (null !== $this->cipher) {
            return $this->cipher->hash($cardNumber, $this->keyStore->get($keyIdentifier));
        }
        
        throw new NoCipherException("No ciphers have been defined.");
    }

Bonus: it is possible to expect a method not to be called, whatever the argument(s) is/are:

    $keyStore->get(Argument::any())->shouldNotBeCalled();

### Let and Let Go

`let` and `letGo` are special methods of `ObjectBehaviour` that are run respectively
_before_ and _after_ each test.

It can be used to factorize the creation of the object under test, which is the same for
every tests.

    function let(KeyStore $keyStore)
    {
        $this->beConstructedWith($keyStore);
    }
    
Wait, how to know if the `KeyStore` object injected in the constructor is the same as
the one used in the tests?

PhpSpec uses the variable name to identify the instances. So every `$keyStore`
will refer to the same mock object.