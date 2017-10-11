<?php

namespace spec\Hipay\MicroToken\Model\Cipher;

use PhpSpec\ObjectBehavior;

/**
 * Class Sha1CipherSpec
 */
class Sha1CipherSpec extends ObjectBehavior
{
    public function it_hashes_data_with_a_secret_key()
    {
        $this
            ->hash("lorem-ipsum_dolor-sit-amet", "m4gick3y")
            ->shouldReturn("d969ef4bf14b6439d66b196b566633c6369c3cb1");
    }
}