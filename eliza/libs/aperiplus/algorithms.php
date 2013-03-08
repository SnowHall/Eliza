<?php
/*
    Compare (or etc) every item in a list with 
    every other item in the list.
*/
class CartesianSelfProduct {

    function __construct($processor, $method, $list) {
        $tally = array();
        foreach($list as $first) {
            foreach($tally as $second) {
                $processor->$method($first, $second);
            }
            $tally[] = $first;
        }
    }
}