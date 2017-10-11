<?php

namespace Hipay\MicroToken\Model\StorageAdapter;

use Hipay\MicroToken\Exception\CorruptedDataException;

/**
 * Class JsonFileStorageAdapter
 */
class JsonFileStorageAdapter implements StorageAdapterInterface
{
    /**
     * @param string $storage
     * @return array
     * @throws CorruptedDataException
     */
    public function load($storage)
    {
        if (file_exists($storage)) {

            $contents = file_get_contents($storage);

            if ("" !== $contents) {
                return $this->getData($storage, $contents);
            }
        }

        return [];
    }

    /**
     * @param string $storage
     * @param array $data
     * @return StorageAdapterInterface
     */
    public function save($storage, array $data)
    {
        file_put_contents($storage, $this->encode($data));

        return $this;
    }

    /**
     * @param string $file
     * @param string $contents
     * @return array
     * @throws CorruptedDataException
     */
    private function getData($file, $contents)
    {
        $data = $this->decode($contents);

        if (is_array($data)) {
            return $data;
        }

        throw new CorruptedDataException(sprintf("The storage '%s' is corrupted.", $file));
    }

    /**
     * @param string $contents
     * @return mixed
     */
    private function decode($contents)
    {
        return json_decode($contents, true);
    }

    /**
     * @param array $data
     * @return string
     */
    private function encode(array $data)
    {
        return json_encode($data);
    }
}