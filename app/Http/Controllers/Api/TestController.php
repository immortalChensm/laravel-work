<?php

namespace App\Http\Controllers\Api;

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
        $request = SymRequest::createFromGlobals();
        
        $dispatcher = new EventDispatcher();
        // ... add some event listeners
        
        // create your controller and argument resolvers
        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();
        
        // instantiate the kernel
        $kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);
        
        // actually execute the kernel, which turns the request into a response
        // by dispatching events, calling a controller, and returning the response
        $response = $kernel->handle($request);
        
        // send the headers and echo the content
        $response->send();
        
        // trigger the kernel.terminate event
        $kernel->terminate($request, $response);
        
    }
}
