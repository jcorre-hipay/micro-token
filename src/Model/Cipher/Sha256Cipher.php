<?php

namespace Hipay\MicroToken\Model\Cipher;

/**
 * Class Sha256Cipher
 */
class Sha256Cipher extends AbstractCipher
{
    /**
     * @return string
     */
    protected function getAlgorithm()
    {
        return "sha256";
    }
}