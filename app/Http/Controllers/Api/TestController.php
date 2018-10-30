<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request as SymRequest;


//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class TestController extends Controller
{
    public function index()
    {
//        $request = SymRequest::createFromGlobals();
//
//        print_r($request->headers);


        //return User::first();
        
        
        $db = new Manager();
        $db->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $db->setEventDispatcher(new Dispatcher(new Container()));
        $db->setAsGlobal();
        $db->bootEloquent();

        $result = Manager::table("users")->where("id","<>",0)->get();
        echo sdfdsfsfs;
        return $result;
    }
}
