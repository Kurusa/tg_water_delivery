<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model {

    protected $table = 'record';
    protected $fillable = ['user_id', 'address', 'phone', 'status', 'name', 'date', 'time', 'count'];

}