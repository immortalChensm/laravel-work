<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/6
 * Time: 0:12
 */
require_once 'Macroable.php';
require_once 'JackSon.php';
require_once 'Tom.php';

JackSon::mixin(new Tom());
echo JackSon::say();
echo JackSon::walk("dog");
echo (new JackSon())->walk("cat");