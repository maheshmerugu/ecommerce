<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePincodesTable extends Migration
{
    public function up()
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('pincode')->index();
            $table->string('area')->nullable();
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pincodes');
    }
}
