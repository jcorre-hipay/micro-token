<?php

namespace Hipay\MicroToken\Model\StorageAdapter;

use Hipay\MicroToken\Exception\CorruptedDataException;

/**
 * Interface StorageAdapterInterface
 */
interface StorageAdapterInterface
{
    /**
     * @param string $storage
     * @return array
     * @throws CorruptedDataException
     */
    public function load($storage);

    /**
     * @param string $storage
     * @param array $data
     * @return StorageAdapterInterface
     */
    public function save($storage, array $data);
}