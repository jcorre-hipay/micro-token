<?php

namespace Hipay\MicroToken\Model\Cipher;

/**
 * Class AbstractCipher
 */
abstract class AbstractCipher implements CipherInterface
{
    /**
     * @param string $data
     * @param string $key
     * @return string
     */
    public function hash($data, $key)
    {
        return hash($this->getAlgorithm(), $key.$data);
    }

    /**
     * @return string
     */
    abstract protected function getAlgorithm();
}