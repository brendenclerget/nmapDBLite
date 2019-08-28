<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $table = "scans";

    protected $guarded = ['id'];

    public function ip() {
        return $this->belongsTo('App\IPAddress','ip_address_id');
    }

    public function ports() {
        return $this->hasMany('App\ScanPort','scan_id','id');
    }

    /**
     * When start date timestap is fetched for scan, mutate into viewable
     *
     * @param  string  $value
     * @return string
     */
    public function scanDate()
    {
        return \Carbon\Carbon::createFromTimestamp($this->start)->format('m/d/Y');
    }

    /**
     * When end date timestap is fetched for scan, mutate into viewable pretty format
     *
     * @param  string  $value
     * @return string
     */
    public function endDatePretty()
    {
        return \Carbon\Carbon::createFromTimestamp($this->end)->format('D M. j, g:i A');
    }

    /**
     * When start date timestap is fetched for scan, mutate into viewable pretty format
     *
     * @param  string  $value
     * @return string
     */
    public function startDatePretty()
    {
        return \Carbon\Carbon::createFromTimestamp($this->end)->format('D M. j, g:i A');
    }
}
