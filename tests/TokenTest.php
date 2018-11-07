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
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenCreationSuccessfully()
    {
        $this->markTestIncomplete("it needs to be implemented!");
    }
}