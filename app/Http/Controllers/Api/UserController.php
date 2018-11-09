<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use Helpers;
    //
    public function index()
    {
        return $this->response->array([1,2,3]);
    }
}
