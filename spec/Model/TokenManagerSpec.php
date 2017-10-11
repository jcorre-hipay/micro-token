<?php

namespace spec\Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\NoCipherException;
use Hipay\MicroToken\Model\Cipher\CipherInterface;
use Hipay\MicroToken\Model\KeyStore;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * Class TokenManagerSpec
 */
class TokenManagerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger, KeyStore $keyStore)
    {
        $this->beConstructedWith($logger, $keyStore);
    }

    function it_creates_a_token_from_a_card_number_and_a_key(
        KeyStore $keyStore,
        CipherInterface $cipher
    ) {
        $keyStore->get(4)->shouldBeCalled()->willReturn("l1br4ry");

        $cipher->hash("372999410121001", "l1br4ry")->shouldBeCalled()->willReturn("5d4122f7fcbbf9d3738176596160a741");

        $this->setCipher($cipher);

        $this->create("372999410121001", 4)->shouldReturn("5d4122f7fcbbf9d3738176596160a741");
    }

    function it_throws_an_exception_when_no_ciphers_have_been_set(
        KeyStore $keyStore
    ) {
        $keyStore->get(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new NoCipherException("No ciphers have been defined."))
            ->during("create", ["372999410121001", 4]);
    }
}