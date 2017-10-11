<?php

namespace Hipay\MicroToken\Model\Cipher;

/**
 * Interface CipherInterface
 */
interface CipherInterface
{
    /**
     * @param string $data
     * @param string $key
     * @return string
     */
    public function hash($data, $key);
}