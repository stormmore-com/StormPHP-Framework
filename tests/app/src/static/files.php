
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" />

    <input type="file" name="tab[]" />
    <input type="file" name="tab[]" />

    <input type="file" name="assoc[a][b][0][0]" />
    <input type="file" name="assoc[a][c]" />
    <input type="file" name="assoc[a][b][1]" />
    <input type="file" name="assoc[d][e]" />
    <input type="file" name="assoc[1][]" />

    <button>Send</button>
</form>


<?php

use Stormmore\Framework\Mvc\IO\Request;

/** @var Request $request */


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

class FileArrayParser
{
    /**
     * Transform standard $_FILES structure array into array of objects
     *
     * @param array $files
     * @return array
     */
    public function parseToObjectArray(array $files): array
    {
        $objects = [];
        foreach ($files as $fieldName => $values) {
            if (is_array($values['name'])) {
                $objects[$fieldName] = $this->parse($values);
            }
            else {
                $object = new stdClass();
                $object->name = $values['name'];
                $object->error = $values['error'];
                $objects[$fieldName] = $object;
            }
        }
        return $objects;
    }

    private function parse(array $files): array
    {
        $_files = [];

        //name d e
        $valuesKeyPath = Collection::getValuesKeyPaths($files);
        foreach($valuesKeyPath as $valueKeyPath) {
            $keys = $valueKeyPath[1];
            $propValue = $valueKeyPath[0];
            $propName = $keys[0];

            $array = &$_files;
            for($i = 1; $i < count($keys); $i++) {
                $key = $keys[$i];
                $exist = array_key_exists($key, $array);
                $last = $i == count($keys) - 1;

                if (!$exist) {
                    if ($last) {
                        $array[$key] = new stdClass();
                    } else {
                        $array[$key] = [];
                    }
                }
                $array = &$array[$key];
            }

            $array = &$_files;
            for($i = 1; $i < count($keys); $i++) {
                $key = $keys[$i];
                $value = &$array[$key];
                if (is_object($value)) {
                    $value->{$propName} = $propValue;
                }
                if (is_array($value)) {
                    $array = &$value;
                }
            }
        }
        return $_files;
    }
}

if ($request->isPost()) {

    $fileParser = new FileArrayParser();
    $files = $fileParser->parseToObjectArray($_FILES);
    echo '<pre>';
    var_dump($files);
    echo '</pre>';
}
