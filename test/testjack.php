<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/6
 * Time: 0:05
 */
require_once 'Jack.php';
require_once 'Macroable.php';
require_once 'JackSon.php';

JackSon::macro('talk',new Jack());
(new JackSon())->talk();
JackSon::talk();