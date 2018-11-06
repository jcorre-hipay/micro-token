<?php

namespace Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\CorruptedDataException;
use Hipay\MicroToken\Exception\NoCipherException;
use Hipay\MicroToken\Exception\UnknownKeyException;
use Hipay\MicroToken\Model\Cipher\CipherInterface;

/**
 * Class TokenManager
 */
class TokenManager
{
    /**
     * @var KeyStore
     */
    private $keyStore;

    /**
     * @var CipherInterface
     */
    private $cipher;

    public function __construct(KeyStore $keyStore)
    {
        $this->keyStore = null;
        $this->cipher = null;
    }

    /**
     * @param CipherInterface $cipher
     * @return TokenManager
     */
    public function setCipher(CipherInterface $cipher)
    {
        return $this;
    }

    /**
     * @param string $cardNumber
     * @param int $keyIdentifier
     * @return string
     * @throws NoCipherException
     * @throws UnknownKeyException
     * @throws CorruptedDataException
     */
    public function create($cardNumber, $keyIdentifier)
    {
        return "";
    }
}