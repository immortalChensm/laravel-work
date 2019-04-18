<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/18
 * Time: 22:58
 */

function talk($param,$next)
{
    print_r(func_get_args());
}
$test = function ($a){
    $b=['a'=>'b'];
    return function ($a)use($b){
        $param = [$a,$b];
        return talk(...$param);
    };
};

$inner = $test(['b'=>'bbb']);

$inner(['b'=>'bbb']);