<?php

namespace spec\Hipay\MicroToken\Model\Cipher;

use PhpSpec\ObjectBehavior;

/**
 * Class Sha256CipherSpec
 */
class Sha256CipherSpec extends ObjectBehavior
{
    public function it_hashes_data_with_a_secret_key()
    {
        $this
            ->hash("lorem-ipsum_dolor-sit-amet", "m4gick3y")
            ->shouldReturn("e0b791fed4dc250d99c5f12addf24755601d660e5557eb9a9ea323f9f3109768");
    }
}