<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use CrudTrait;
    protected $table = 'users';
    protected $fillable=[
        'name',
        'email',
        'username',
        'is_blocked'
    ];
}
