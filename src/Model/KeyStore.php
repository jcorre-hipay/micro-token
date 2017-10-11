<?php

namespace Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\CorruptedDataException;
use Hipay\MicroToken\Exception\ExistingKeyException;
use Hipay\MicroToken\Exception\UnknownKeyException;
use Hipay\MicroToken\Model\StorageAdapter\StorageAdapterInterface;

/**
 * Class KeyStore
 */
class KeyStore
{
    /**
     * @var StorageAdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $storage;

    /**
     * KeyStore constructor.
     * @param StorageAdapterInterface $adapter
     * @param string $storage
     */
    public function __construct(StorageAdapterInterface $adapter, $storage)
    {
        $this->adapter = $adapter;
        $this->storage = $storage;
    }

    /**
     * @param int $id
     * @return string
     * @throws UnknownKeyException
     * @throws CorruptedDataException
     */
    public function get($id)
    {
        $data = $this->load();

        if (!isset($data[$id])) {
            throw new UnknownKeyException(sprintf("Key #%s is not registered.", $id));
        }

        return $data[$id];
    }

    /**
     * @param int $id
     * @param string $value
     * @return KeyStore
     * @throws ExistingKeyException
     * @throws CorruptedDataException
     */
    public function register($id, $value)
    {
        $data = $this->load();

        if (isset($data[$id])) {
            throw new ExistingKeyException(sprintf("Key #%s is already registered.", $id));
        }

        $data[$id] = $value;

        $this->save($data);

        return $this;
    }

    /**
     * @return array
     * @throws CorruptedDataException
     */
    private function load()
    {
        return $this->adapter->load($this->storage);
    }

    /**
     * @param array $data
     * @return KeyStore
     */
    private function save(array $data)
    {
        $this->adapter->save($this->storage, $data);

        return $this;
    }
}