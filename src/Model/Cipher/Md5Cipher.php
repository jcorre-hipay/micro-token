<?php

namespace Hipay\MicroToken\Model\Cipher;

/**
 * Class Md5Cipher
 */
class Md5Cipher extends AbstractCipher
{
    /**
     * @return string
     */
    protected function getAlgorithm()
    {
        return "md5";
    }
}