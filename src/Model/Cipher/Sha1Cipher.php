<?php

namespace Hipay\MicroToken\Model\Cipher;

/**
 * Class Sha1Cipher
 */
class Sha1Cipher extends AbstractCipher
{
    /**
     * @return string
     */
    protected function getAlgorithm()
    {
        return "sha1";
    }
}