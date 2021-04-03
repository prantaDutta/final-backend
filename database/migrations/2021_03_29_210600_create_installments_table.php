<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('loan_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
//            $table->unique(['user_id', 'loan_id']);
            $table->string('unique_installment_id');
            $table->decimal('amount',12);
//            $table->string('current_month');
            $table->enum('status', ['unpaid', 'paid', 'due']);
            $table->decimal('penalty_amount',12);
            $table->decimal('total_amount',12);
            $table->integer('installment_no');
            $table->timestamp('due_date');
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
        Schema::dropIfExists('installments');
    }
}
