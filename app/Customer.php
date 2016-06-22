<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['first_name', 'last_name','email','card_name','card_expiry','card_number','cvc'];

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
