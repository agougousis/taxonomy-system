<?php

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function flatten($input_array){
    $output = array();
    array_walk_recursive($input_array, function ($current) use (&$output) {
        $output[] = $current;
    });
    return $output;
}