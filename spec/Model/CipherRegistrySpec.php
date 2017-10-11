<?php

namespace spec\Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\UnsupportedAlgorithmException;
use Hipay\MicroToken\Model\Cipher\CipherInterface;
use PhpSpec\ObjectBehavior;

/**
 * Class CipherRegistrySpec
 */
class CipherRegistrySpec extends ObjectBehavior
{
    function let(CipherInterface $md5Cipher, CipherInterface $sha1Cipher)
    {
        $this->beConstructedWith(
            [
                "md5" => $md5Cipher,
                "sha1" => $sha1Cipher,
            ]
        );
    }

    function it_returns_the_cipher_corresponding_to_the_algorithm(CipherInterface $md5Cipher)
    {
        $this->get("md5")->shouldReturn($md5Cipher);
    }

    function it_throws_an_exception_when_the_algorithm_is_not_supported()
    {
        $this
            ->shouldThrow(new UnsupportedAlgorithmException("Algorithm 'sha256' is not supported."))
            ->during("get", ["sha256"]);
    }
}