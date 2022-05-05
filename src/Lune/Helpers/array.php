<?php

/**
 * Check if an array is associative
 * @param array $array
 * @return bool
 */
function isAssociative(array $array): bool {
    if (empty($array)) {
        return false;
    }

    $keys = array_keys($array);

    return array_keys($keys) !== $keys;
}

/**
 * Retrieve the first element of an array
 * @param array $array
 * @return null|mixed
 */
function first(array $array) {
    if (empty($array)) {
        return null;
    }

    return $array[array_key_first($array)];
}

/**
 * Retrieve the last element of an array
 * @param array $array
 * @return null|mixed
 */
function last(array $array) {
    if (empty($array)) {
        return null;
    }

    return $array[array_key_last($array)];
}

/**
 * Retrieve new array using the given callback as a condition
 * @param array $array
 * @param callable $callback
 * @return array
 */
function filter(array $array, callable $callback) {
    $filteredArray = [];

    $isAssociative = isAssociative($array);

    foreach ($array as $key => $value) {
        if (call_user_func($callback, $value, $key)) {
            if ($isAssociative) {
                $filteredArray[$key] = $value;
                continue;
            }

            array_push($filteredArray, $value);
        }
    }

    return $filteredArray;
}
