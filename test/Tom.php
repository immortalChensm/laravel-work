<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/6
 * Time: 0:10
 */

class Tom
{
    public function say()
    {
        return function (){
            return "tom say php";
        };
    }

    public function walk()
    {
        return function ($pet){
            return "tom walked ".$pet;
        };
    }
}