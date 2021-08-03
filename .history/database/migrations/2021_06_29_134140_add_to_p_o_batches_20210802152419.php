<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToPOBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_o_batches', function (Blueprint $table) {
            //
            $table->bigInteger('tag_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_o_batches', function (Blueprint $table) {
            //
            $table->dropColumn('tag_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('r_r_batches');
    }
}
