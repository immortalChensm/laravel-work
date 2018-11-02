<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    //User模型继承的trait类，该模型能调用以下继承的trait类里所有的方法
    use Authenticatable, Authorizable, CanResetPassword;
}

/**

 * 该User类继承了
 * 如下类
 * Authenticatable, Authorizable, CanResetPassword
 * Model类
 * 所以任何类如果继承了Auth/User类，将能继承以上提供的类
 **/