<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\AuthRequest;
class AuthController extends BaseController
{
    //
    public function users(AuthRequest $request)
    {
        $credentials = ['email'=>$request['email'],'password'=>$request['password']];
        if($token = auth()->attempt($credentials)){
            return $this->response->array(['token'=>$token,'userinfo'=>auth()->user()]);
        }else{
            return $this->response->array(['code'=>0,'msg'=>'login fail']);
        }
       
    }
    
    public function info()
    {
        return $this->response->array(['code'=>1,'msg'=>'request ok','data'=>auth()->user()]);
    }
}
