<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScanPort extends Model
{
    protected $table = "scan_ports";

    protected $guarded = ['id'];

    public function scan() {
        return $this->belongsTo('App\Scan');
    }
}
