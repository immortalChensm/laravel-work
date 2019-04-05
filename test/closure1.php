<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/5
 * Time: 23:24
 */

class Test{

    public function __construct($num)
    {
        $this->num = $num;
    }

    public function getClosure()
    {
        return function (){return $this->num;};
    }
}

$test1 = new Test(1);
$test2 = new Test(2);

$testFun = function (){
    return $this->num+100;
};

$fun = $test1->getClosure();
$fun->bindTo($test2);
echo $fun();

//$testFunction = $testFun->bindTo($test2,'Test');
$testFunction = $testFun->bindTo($test2);
echo $testFunction();