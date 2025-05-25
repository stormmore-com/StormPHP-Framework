<?php

namespace Stormmore\Framework\Std;

use Countable;
use ArrayAccess;

class Collection implements Countable, ArrayAccess
{
    public function __construct(private array $collection = [])
    {
    }

    public function exists(float|int|string $key): bool
    {
        return array_key_exists($key, $this->collection);
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function add(mixed $value): void
    {
        $this->collection[] = $value;
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->collection)) {
            return $this->collection[$key];
        }
        return $default;
    }

    public function addAt(int|float|string $key, mixed $value): void
    {
        $this->collection[$key] = $value;
    }

    /**
     * Return array values and keys leading to it e.g.
     * for input array
     * [
     *  'key' => [
     *    'subkey' => 2
     *  ]
     * ]
     * method returns
     * array (
     *     array(2, array('key1', 'subkey')
     * )
     *
     * @param array $array
     * @return array
     */
    public static function getValuesKeyPaths(array $array): array
    {
        $result = array();
        self::_getValueKeyPath($array, [], $result);
        return $result;
    }

    private static function _getValueKeyPath(array $array, array $keys, array &$result): void
    {
        foreach($array as $key => $value) {
            $_keys = $keys;
            $_keys[] = $key;
            if (is_array($value)) {
                $subArray = &$array[$key];
                self::_getValueKeyPath($subArray, $_keys, $result);
            }
            else {
                $result[] = array($value, $_keys);
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->exists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->collection[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }
}