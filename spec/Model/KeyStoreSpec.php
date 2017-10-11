<?php

namespace spec\Hipay\MicroToken\Model;

use Hipay\MicroToken\Exception\ExistingKeyException;
use Hipay\MicroToken\Exception\UnknownKeyException;
use Hipay\MicroToken\Model\StorageAdapter\StorageAdapterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class KeyStoreSpec
 */
class KeyStoreSpec extends ObjectBehavior
{
    function let(StorageAdapterInterface $adapter)
    {
        $adapter
            ->load("/tmp/data.db")
            ->shouldBeCalled()
            ->willReturn(
                [
                    1 => "ban4na",
                    4 => "l1br4ry",
                    5 => "ook0ok",
                ]
            );

        $this->beConstructedWith($adapter, "/tmp/data.db");
    }

    function it_returns_the_key_by_its_identifier()
    {
        $this->get(4)->shouldReturn("l1br4ry");
    }

    function it_throws_an_exception_when_getting_an_unregistered_key()
    {
        $this
            ->shouldThrow(new UnknownKeyException("Key #2 is not registered."))
            ->during("get", [2]);
    }

    function it_registers_a_key(
        StorageAdapterInterface $adapter
    ) {
        $data = [
            1 => "ban4na",
            4 => "l1br4ry",
            5 => "ook0ok",
            3 => "m4gic"
        ];

        $adapter->save("/tmp/data.db", $data)->shouldBeCalled();

        $this->register(3, "m4gic");
    }

    function it_throws_an_exception_when_registering_an_existing_key(
        StorageAdapterInterface $adapter
    ) {
        $adapter->save("/tmp/data.db", Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new ExistingKeyException("Key #4 is already registered."))
            ->during("register", [4, "m4gic"]);
    }
}