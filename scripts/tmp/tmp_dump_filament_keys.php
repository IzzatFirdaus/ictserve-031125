<?php

$path = $argv[1];
$data = require $path;
function flatten($arr, $prefix = '')
{
    foreach ($arr as $key => $value) {
        $full = $prefix === '' ? $key : $prefix.'.'.$key;
        if (is_array($value)) {
            flatten($value, $full);
        } else {
            echo $full."\n";
        }
    }
}
flatten($data);
