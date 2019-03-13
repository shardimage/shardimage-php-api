<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base;

use shardimage\shardimagephpapi\base\exceptions\InvalidCallException;
use shardimage\shardimagephpapi\base\exceptions\UnknownPropertyException;

/**
 * Base object.
 */
class BaseObject
{

    /**
     * Values for toArray() function. By overwriting, it's possible to add extra attributes,
     * like private properties used by magic methods.
     * @return array
     */
    protected function getToArrayAttributes()
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * Creates the object, populates the properties and calls the "init" method.
     *
     * @param array $config Predefined properties
     */
    public function __construct($config = [])
    {
        foreach ((array) $config as $property => $value) {
            $this->$property = $value;
        }
        $this->init();
    }

    /**
     * Custom initialization.
     */
    protected function init()
    {
    }

    /**
     * Returns the value of a non-defined property, it there is a getter method.
     *
     * @param string $name Property
     *
     * @return mixed
     *
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set'.$name)) {
            throw new InvalidCallException('Getting write-only property: '.get_class($this).'::'.$name);
        } else {
            return null;
        }
    }

    /**
     * Sets the value of a non-defined property, if there is a setter method.
     *
     * @param string $name  Property
     * @param mixed  $value Value
     *
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get'.$name)) {
            throw new InvalidCallException('Setting read-only property: '.get_class($this).'::'.$name);
        } else {
            $this->$name = $value;
        }
    }

    /**
     * Does the non-defined property with a getter exist and is it not NULL?
     * 
     * @param string $name Property
     *
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    /**
     * Sets the value of a non-defined property to NULL, if there is a setter.
     *
     * @param string $name Property
     *
     * @throws InvalidCallException
     */
    public function __unset($name)
    {
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get'.$name)) {
            throw new InvalidCallException('Unsetting read-only property: '.get_class($this).'::'.$name);
        }
    }

    /**
     * Creates an object from the given parameters.
     *
     * @param string $name name of the class property where the new object will be
     * @param string $class namespace of the class
     */
    protected function ensureClass($name, $class)
    {
        if (!is_null($this->$name) && !$this->$name instanceof $class) {
            $this->$name = new $class($this->$name);
        }
    }

    /**
     * Converts the object an array.
     *
     * @param bool $excludeEmpty whether to remove empty element
     * @return array
     */
    public function toArray($excludeEmpty = false)
    {
        $result = [];
        foreach ($this->getToArrayAttributes() as $key) {
            $value = $this->$key;
            if ($value instanceof self) {
                $value = $value->toArray($excludeEmpty);
            }
            $result[$key] = $value;
        }
        if ($excludeEmpty) {
            $result = array_filter($result, function ($value) {
                return isset($value) && !(is_array($value) && empty($value));
            });
        }

        return $result;
    }
}
