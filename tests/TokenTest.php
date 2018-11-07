<?php

namespace Hipay\MicroToken\Tests;

use Hipay\MicroToken\Exception\ExistingKeyException;
use Hipay\MicroToken\Exception\NoCipherException;
use Hipay\MicroToken\Exception\UnknownKeyException;
use Hipay\MicroToken\Exception\UnsupportedAlgorithmException;
use Hipay\MicroToken\Model\Cipher\Md5Cipher;
use Hipay\MicroToken\Model\Cipher\Sha1Cipher;
use Hipay\MicroToken\Model\Cipher\Sha256Cipher;
use Hipay\MicroToken\Model\CipherRegistry;
use Hipay\MicroToken\Model\KeyStore;
use Hipay\MicroToken\Model\StorageAdapter\StorageAdapterInterface;
use Hipay\MicroToken\Model\TokenManager;
use Psr\Log\LoggerInterface;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $storageAdapter;

    /** @var CipherRegistry */
    private $cipherRegistry;

    /** @var KeyStore */
    private $keyStore;

    /** @var TokenManager */
    private $tokenManager;

    protected function setUp()
    {
        $this->storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();

        $ciphers = [
            "md5" => new Md5Cipher(),
            "sha1" => new Sha1Cipher(),
            "sha256" => new Sha256Cipher(),
        ];

        $this->cipherRegistry = new CipherRegistry($ciphers);
        $this->keyStore = new KeyStore($this->storageAdapter, "/tmp/micro-token.data");
        $this->tokenManager = new TokenManager(
            $this->getMockBuilder(LoggerInterface::class)->getMock(),
            $this->keyStore
        );

        $this->storageAdapter
            ->expects(static::once())
            ->method("save")
            ->with("/tmp/micro-token.data", [4 => "l1br4ry"]);

        $this->keyStore->register(4, "l1br4ry");
    }

    /**
     * @dataProvider provideCipherAlgorithms
     */
    public function testTokenCreationSuccessfully($expectedToken, $algorithm)
    {
        $this->storageAdapter
            ->expects(static::once())
            ->method("load")
            ->with("/tmp/micro-token.data")
            ->willReturn([4 => "l1br4ry"]);

        $this->tokenManager->setCipher($this->cipherRegistry->get($algorithm));
        $actualToken = $this->tokenManager->create("372999410121001", 4);

        static::assertSame($expectedToken, $actualToken);
    }

    public function testFailureWhenKeyIsRegisteredTwice()
    {
        $this->storageAdapter
            ->expects(static::once())
            ->method("load")
            ->with("/tmp/micro-token.data")
            ->willReturn([4 => "l1br4ry"]);

        $this->expectException(ExistingKeyException::class);

        $this->keyStore->register(4, "l1br4ry");
    }

    public function testFailureWhenNoCiphersSet()
    {
        $this->storageAdapter
            ->expects(static::never())
            ->method("load");

        $this->expectException(NoCipherException::class);

        $this->tokenManager->create("372999410121001", 4);
    }

    public function testFailureWhenKeyIsNotRegistered()
    {
        $this->storageAdapter
            ->expects(static::once())
            ->method("load")
            ->with("/tmp/micro-token.data")
            ->willReturn([4 => "l1br4ry"]);

        $this->expectException(UnknownKeyException::class);

        $this->tokenManager->setCipher($this->cipherRegistry->get("md5"));
        $this->tokenManager->create("372999410121001", 6);
    }

    public function testFailureWhenAlgorithmIsNotSupported()
    {
        $this->storageAdapter
            ->expects(static::never())
            ->method("load");

        $this->expectException(UnsupportedAlgorithmException::class);

        $this->tokenManager->setCipher($this->cipherRegistry->get("unexpected"));
    }

    public function provideCipherAlgorithms()
    {
        return [
            "md5" => ["5d4122f7fcbbf9d3738176596160a741", "md5"],
            "sha1" => ["62f6446c839e3749a938fa7d468c79f5d247c3c2", "sha1"],
            "sha256" => ["c061628b32afd532463daf2b771cb7306cbbfea3857bcd21f1785c7eed1efb54", "sha256"],
        ];
    }
}