<?php

namespace spec\Hipay\MicroToken\Model\StorageAdapter;

use Hipay\MicroToken\Exception\CorruptedDataException;
use PhpSpec\ObjectBehavior;

/**
 * Class JsonFileStorageAdapterSpec
 */
class JsonFileStorageAdapterSpec extends ObjectBehavior
{
    private $file;

    function let()
    {
        $this->file = tempnam(sys_get_temp_dir(), uniqid());
    }

    function letGo()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    function it_loads_data_from_a_json_file()
    {
        file_put_contents($this->file, '{"message":"Ook! Ook?","priority":4}');

        $this->load($this->file)->shouldReturn(["message" => "Ook! Ook?", "priority" => 4]);
    }

    function it_loads_empty_data_when_the_file_does_not_exist()
    {
        unlink($this->file);

        $this->load($this->file)->shouldReturn([]);
    }

    function it_loads_empty_data_when_the_file_is_empty()
    {
        file_put_contents($this->file, "");

        $this->load($this->file)->shouldReturn([]);
    }

    function it_throws_an_exception_when_the_file_does_not_contain_a_json_array()
    {
        file_put_contents($this->file, '"not a valid data"');

        $this
            ->shouldThrow(new CorruptedDataException("The storage '".$this->file."' is corrupted."))
            ->during("load", [$this->file]);
    }

    function it_creates_the_file_and_saves_data()
    {
        unlink($this->file);

        $data = ["city" => "ankh-morpork", "year" => 1983];

        $this->save($this->file, $data);

        $this->load($this->file)->shouldReturn($data);
    }

    function it_override_existing_data_when_saving()
    {
        file_put_contents($this->file, '{"message":"Ook! Ook?","priority":4}');

        $data = ["city" => "ankh-morpork", "year" => 1983];

        $this->save($this->file, $data);

        $this->load($this->file)->shouldReturn($data);
    }
}