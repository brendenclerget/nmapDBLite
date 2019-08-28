<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IPAddress extends Model
{
    protected $table = "ip_addresses";

    protected $guarded = ['id'];

    public function scans() {
        return $this->hasMany('App\Scan','ip_address_id','id');
    }
}
