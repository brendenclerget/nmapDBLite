<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Scans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ip_address_id');
            $table->integer('start');
            $table->integer('end');
            $table->string('state');
            $table->string('reason');
            $table->string('reason_ttl');
            $table->string('host')->nullable();
            $table->string('host_type')->nullable();
            $table->integer('srtt');
            $table->integer('rttvar');
            $table->integer('to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scans');
    }
}
