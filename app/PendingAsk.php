<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingAsk extends Model
{
    protected $fillable = [
        'address',
        'amount'
    ];
}
