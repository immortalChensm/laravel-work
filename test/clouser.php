<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/5
 * Time: 23:09
 */

class Person{
    private static $money = 100;
    private $age = 18;
    protected $idcard = 123456;
    public $name = "jack";

    public function talk()
    {
        echo "hi,php";
    }
}

$money = function (){
  echo Person::$money;
};

$age = function (){
    echo $this->age;
};

$idcard = function (){
    echo $this->idcard;
};

$name = function (){
    echo $this->name;
};

$talk = function (){
    echo $this->talk();
};

$bindMoney = Closure::bind($money,null,new Person());
echo $bindMoney();

$bindMoney = Closure::bind($money,new Person(),new Person());
echo $bindMoney();

$bindMoney = Closure::bind($money,new Person(),'Person');
echo $bindMoney();

$bindMoney = Closure::bind($money,null,'Person');
echo $bindMoney();

//$bindAge = Closure::bind($age,new Person(),new Person());
//$bindAge = Closure::bind($idcard,new Person(),new Person());
$bindAge = Closure::bind($name,null,'Person');
//$bindAge = Closure::bind($age,null,'Person');
echo $bindAge();