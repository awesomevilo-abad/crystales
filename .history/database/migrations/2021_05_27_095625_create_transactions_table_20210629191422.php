<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id');
            $table->string('id_prefix');
            $table->bigInteger('id_no');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('department');
            $table->string('transaction_id');
            $table->bigInteger('tag_id')->unique();
            $table->bigInteger('document_id')->nullable();
            $table->string('document_type')->nullable();
            $table->dateTime('document_date')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->string('category')->nullable();
            $table->bigInteger('company_id');
            $table->string('company');
            $table->bigInteger('supplier_id');
            $table->string('supplier');
            $table->float('po_total_amount')->nullable();
            $table->float('po_total_qty')->nullable();
            $table->float('rr_total_qty')->nullable();
            $table->float('referrence_total_amount')->nullable();
            $table->float('referrence_total_amount')->nullable();
            $table->dateTime('date_requested');
            $table->string('remarks')->nullable();
            $table->string('payment_type');
            $table->string('status');
            $table->bigInteger('reason_id')->nullable();
            $table->string('reason')->nullable();
            $table->bigInteger('document_no');
            $table->float('document_amount')->nullable();
            $table->string('pcf_date')->nullable();
            $table->string('pcf_letter')->nullable();
            $table->string('utilities_from')->nullable();
            $table->string('utilities_to')->nullable();

            $table->timestamps();

            // TAGGINGS
            // $table->foreign('transaction_id')->references('transaction_id')->on('taggings')->onDelete('cascade');

            // // GASES
            // $table->foreign('tag_id')->references('tag_id')->on('filings')->onDelete('cascade');

            // // ASSOCIATES
            // $table->foreign('tag_id')->references('tag_id')->on('associates')->onDelete('cascade');

            // // SPECIALISTS
            // $table->foreign('tag_id')->references('tag_id')->on('specialists')->onDelete('cascade');

            // // MATCHES
            // $table->foreign('tag_id')->references('tag_id')->on('matches')->onDelete('cascade');

            // // RETURN VOUCHERS
            // $table->foreign('tag_id')->references('tag_id')->on('return_vouchers')->onDelete('cascade');

            // // APPROVERS
            // $table->foreign('tag_id')->references('tag_id')->on('approvers')->onDelete('cascade');

            // // CHEQUECREATIONS
            // $table->foreign('tag_id')->references('tag_id')->on('cheque_creations')->onDelete('cascade');

            // // TREASURIES
            // $table->foreign('tag_id')->references('tag_id')->on('treasuries')->onDelete('cascade');

            // // CHEQUERELEASEDS
            // $table->foreign('tag_id')->references('tag_id')->on('cheque_releaseds')->onDelete('cascade');

            // // CHEQUECLEARINGS
            // $table->foreign('tag_id')->references('tag_id')->on('cheque_clearings')->onDelete('cascade');

            // // CHEQUE
            // $table->foreign('transaction_id')->references('transaction_id')->on('cheque_tables')->onDelete('cascade');

            // ------------- SUBTABLE TRANSACTION

            // // POBATCH
            // $table->foreign('po_id')->references('id')->on('p_o_batches')->onDelete('cascade');

            // // REFERRENCEBATCH
            // $table->foreign('referrence_id')->references('id')->on('referrence_batches')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
