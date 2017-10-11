<?php

namespace Hipay\MicroToken;

use Hipay\MicroToken\Exception\DependencyInjectionException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class MicroTokenServiceProvider
 */
class MicroTokenServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * MicroTokenServiceProvider constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container A container instance
     */
    public function register(Container $container)
    {
        foreach ($this->configuration as $service => $configuration) {
            $container[$service] = $this->getServiceBuilder($service, $configuration);
        }
    }

    /**
     * @param string $service
     * @param array $configuration
     * @return \Closure
     */
    private function getServiceBuilder($service, array $configuration)
    {
        return function (Container $container) use ($service, $configuration) {

            if (!isset($configuration["class"])) {
                throw new DependencyInjectionException(
                    sprintf("Missing 'class' key to the definition of service '%s'", $service)
                );
            }

            $arguments = isset($configuration["arguments"]) ? $configuration["arguments"] : [];

            return $this->create($container, $configuration["class"], $arguments);
        };
    }

    /**
     * @param Container $container
     * @param string $className
     * @param array $arguments
     * @return object
     */
    private function create(Container $container, $className, array $arguments)
    {
        $reflection = new \ReflectionClass($className);

        return $reflection->newInstanceArgs($this->parseArguments($container, $arguments));
    }

    /**
     * @param Container $container
     * @param array $arguments
     * @return array
     */
    private function parseArguments(Container $container, array $arguments)
    {
        foreach ($arguments as $key => $value) {

            if (is_array($value)) {
                $arguments[$key] = $this->parseArguments($container, $value);
            }

            if (is_string($value)) {
                $arguments[$key] = $this->translateInjectionArgument($container, $value);
            }
        }

        return $arguments;
    }

    /**
     * @param Container $container
     * @param string $value
     * @return string
     */
    private function translateInjectionArgument(Container $container, $value)
    {
        if (0 === strpos($value, "@")) {
            return $container[substr($value, 1)];
        }

        $replace = function ($matches) use ($container) {
            return empty($matches[1]) ? "%" : $container[$matches[1]];
        };

        return preg_replace_callback("/%([^%]*)%/", $replace, $value);
    }
}