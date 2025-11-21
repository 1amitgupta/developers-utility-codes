<?php

function normalizeGlnArrayKeyToString($args, $implode_string = ', ')
{
    if (get_class($args) === 'MongoDB\Model\BSONArray' || get_class($args) === 'MongoDB\Model\BSONDocument') {
        $args = $args->getArrayCopy();
    }

    if (is_string($args)) {
        return $args;
    }

    $flatten = function ($array) use (&$flatten) {
        $result = [];
        foreach ($array as $item) {
            if (get_class($item) === 'MongoDB\Model\BSONArray' || get_class($item) === 'MongoDB\Model\BSONDocument') {
                $item = $item->getArrayCopy();
            }

            if (is_array($item)) {
                $result = array_merge($result, $flatten($item));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    };

    $string = implode($implode_string, $flatten($args));
    return $string;
}
