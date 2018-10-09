<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request as SymRequest;
class TestController extends Controller
{
    public function index()
    {
        $request = SymRequest::createFromGlobals();
        var_dump($request);
    }
}
