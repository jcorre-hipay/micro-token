<?php

namespace Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\UnsupportedAlgorithmException;
use Hipay\MicroToken\Model\Cipher\CipherInterface;

/**
 * Class CipherRegistry
 */
class CipherRegistry
{
    /**
     * @var CipherInterface[]
     */
    private $ciphers;

    /**
     * CipherRegistry constructor.
     * @param CipherInterface[] $ciphers
     */
    public function __construct(array $ciphers = [])
    {
        $this->ciphers = $ciphers;
    }

    /**
     * @param string $algorithm
     * @return CipherInterface
     * @throws UnsupportedAlgorithmException
     */
    public function get($algorithm)
    {
        if (isset($this->ciphers[$algorithm])) {
            return $this->ciphers[$algorithm];
        }

        throw new UnsupportedAlgorithmException(sprintf("Algorithm '%s' is not supported.", $algorithm));
    }
}