<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/18
 * Time: 22:58
 */


$test = function ($a){
    echo 0;
    echo PHP_EOL;
    return function ($a){
        echo 1;
        echo PHP_EOL;
        return function ($a){
            echo 2;
            echo PHP_EOL;
            return function ($a){
              echo 3;
              echo PHP_EOL;

            };
        };
    };
};

print_r($test(1));