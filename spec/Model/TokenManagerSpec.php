<?php

namespace spec\Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\NoCipherException;
use Hipay\MicroToken\Model\Cipher\CipherInterface;
use Hipay\MicroToken\Model\KeyStore;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TokenManagerSpec
 */
class TokenManagerSpec extends ObjectBehavior
{
    function it_creates_a_token_from_a_card_number_and_a_key()
    {

    }

    function it_throws_an_exception_when_no_ciphers_have_been_set()
    {

    }
}