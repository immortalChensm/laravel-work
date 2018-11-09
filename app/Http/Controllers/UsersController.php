<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPosts;
use Illuminate\Http\Request;

use App\User;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

class UsersController extends Controller
{
    //

    public function index()
    {

        $db = new Manager();
        $db->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'laravel',
            'username'  => 'root',
            'password'  => '123456',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $db->setEventDispatcher(new Dispatcher(new Container()));
        $db->setAsGlobal();
        $db->bootEloquent();

        $result = Manager::table("users")->where("id","<>",0)->get();

        //auth('web')->attempt(['a']);

        //$user = User::all();
       // $this->authorize('update',$user);
        return $result;
    }


    public function test(UserPosts $request)
    {
        $a = ['a','b','c','d'];
        $b = collect($a);
        print_r($request->all());
    }

    public function abc()
    {
        //$this->validate();
        $a = "test";
        print_r($request->all());
    }
    
    
}
