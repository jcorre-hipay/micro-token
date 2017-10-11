<?php

namespace spec\Hipay\MicroToken\Model\Cipher;

use PhpSpec\ObjectBehavior;

/**
 * Class Md5CipherSpec
 */
class Md5CipherSpec extends ObjectBehavior
{
    public function it_hashes_data_with_a_secret_key()
    {
        $this
            ->hash("lorem-ipsum_dolor-sit-amet", "m4gick3y")
            ->shouldReturn("d2308eaebda52178fb500e809ad19c22");
    }
}