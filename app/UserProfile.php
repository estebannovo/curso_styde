<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use SoftDeletes;

    //protected $fillable = ['bio', 'twitter', 'profession_id'];

    protected $guarded = [];
}
