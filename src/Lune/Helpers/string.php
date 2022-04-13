<?php

/**
 * Convert string into snake_case.
 * @param string $str
 * @return string
 */
function snake_case(string $str) {
    $str = preg_replace('/\s+|-+|_+/', '_', trim($str));
    $str = preg_replace('/_+/', '_', trim($str));
    $str = preg_replace_callback('/_[A-Z]/', fn ($m) => "_".strtolower($m[0][1]), $str);
    $str = preg_replace_callback('/[a-z][A-Z]/', fn ($m) => $m[0][0]."_".strtolower($m[0][1]), $str);
    $str[0] = strtolower($str[0]);

    return $str;
}

/**
 * Convert string into camelCase.
 * @param string $str
 * @return string
 */
function camel_case(string $str) {
    $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
    $str = trim($str);
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    $str = lcfirst($str);

    return $str;
}
