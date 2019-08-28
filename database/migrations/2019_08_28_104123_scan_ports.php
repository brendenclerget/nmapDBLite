<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScanPorts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_ports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('scan_id');
            $table->string('protocol',16);
            $table->integer('port');
            $table->string('state',16);
            $table->string('reason',16);
            $table->integer('reason_ttl');
            $table->string('service',16);
            $table->string('method',32);
            $table->integer('conf');
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
        Schema::dropIfExists('scan_ports');
    }
}
