<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use stdClass;
use Stormmore\Framework\Std\Collection;

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