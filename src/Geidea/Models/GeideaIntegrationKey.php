<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Models;

use Illuminate\Database\Eloquent\Model;

class GeideaIntegrationKey extends Model
{
    protected $table = 'paymob_integration_keys';

    protected $guarded = ['id'];


    public function getUsernameAttribute()
    {
        return $this->attributes['merchant_code'];
    }

    public function getPasswordAttribute()
    {
        return $this->attributes['api_key'];
    }
}
