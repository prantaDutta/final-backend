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
    public function up(): void
    {
        Schema::create('loans', static function (Blueprint $table) {
            $table->id();
            $table->decimal('loan_amount');
            $table->string('unique_loan_id');
            $table->json('lender_data')->nullable();
            $table->enum('loan_mode',
                ['processing', 'ongoing', 'finished', 'failed']);
            $table->integer('loan_duration');
            $table->integer('interest_rate');
            $table->decimal('amount_with_interest',12);
            $table->decimal('company_fees',12);
            $table->decimal('amount_with_interest_and_company_fees',12);
            $table->decimal('monthly_installment',12);
            $table->decimal('monthly_installment_with_company_fees',12);
//            $table->integer('total_installment');
//            $table->timestamp('loan_start_date');
//            $table->timestamp('loan_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
}
