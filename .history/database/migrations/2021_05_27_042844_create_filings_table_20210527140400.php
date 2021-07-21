<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tag_id')->unsigned();
            $table->string('receipt_type');
            $table->date('date_received');
            $table->string('status');
            $table->date('date_status');
            $table->float('witholding_tax');
            $table->float('percentage_tax');
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
        Schema::dropIfExists('filings');
    }
}