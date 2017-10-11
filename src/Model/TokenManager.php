<?php

namespace Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\CorruptedDataException;
use Hipay\MicroToken\Exception\NoCipherException;
use Hipay\MicroToken\Exception\UnknownKeyException;
use Hipay\MicroToken\Model\Cipher\CipherInterface;
use Psr\Log\LoggerInterface;

/**
 * Class TokenManager
 */
class TokenManager
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var KeyStore
     */
    private $keyStore;

    /**
     * @var CipherInterface
     */
    private $cipher;

    /**
     * TokenManager constructor.
     * @param LoggerInterface $logger
     * @param KeyStore $keyStore
     */
    public function __construct(LoggerInterface $logger, KeyStore $keyStore)
    {
        $this->logger = $logger;
        $this->keyStore = $keyStore;
        $this->cipher = null;
    }

    /**
     * @param CipherInterface $cipher
     * @return TokenManager
     */
    public function setCipher(CipherInterface $cipher)
    {
        $this->cipher = $cipher;

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
        if (null !== $this->cipher) {
            return $this->cipher->hash($cardNumber, $this->keyStore->get($keyIdentifier));
        }

        throw new NoCipherException("No ciphers have been defined.");
    }
}