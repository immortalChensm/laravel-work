<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/1/26
 * Time: 18:28
 */

1、框架在启动的时候，会运行数据库服务提供者类
  Illuminate\Database\DatabaseServiceProvider::class
  从而完成数据库管理器，连接工厂注册

  当然框架在启动的时候，数据库的伪装类会先注册即DB
  当使用DB::xxx的时候自动触发伪装基类，从而从Application检索到db并实例化
  DatabaseManager类，同时完成ConnectionFactory的实例
  具体使用手册在https://github.com/illuminate/database

  当使用DB::xxx时，会根据config/database.php的配置参数，实例化对应的XXXConnection对象
  如当前的驱动是mysql时，则会实例化MysqlConnection类，使用PDO实例化

  首先是先创建PDO连接
  再创建连接
  当使用DB::table时返回的是Builder类【查询构造器】
  其它时候返回Connection
