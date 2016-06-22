<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'price'];

    function Customer()
    {
        return $this->belongsTo('App\Customer');
    }
}
