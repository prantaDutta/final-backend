<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', static function (Blueprint $table) {
            $table->id();
            $table->decimal('loan_amount');
            $table->enum('loan_mode',['processing','ongoing','finished']);
            $table->integer('loan_duration');
            $table->integer('interest_rate');
            $table->decimal('amount_with_interest');
            $table->decimal('company_fees');
            $table->decimal('amount_with_interest_and_company_fees');
            $table->decimal('monthly_installment');
            $table->decimal('monthly_installment_with_company_fees');
            $table->timestamp('loan_start_date')->nullable();
            $table->timestamp('loan_end_date')->nullable();
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
        Schema::dropIfExists('loans');
    }
}
