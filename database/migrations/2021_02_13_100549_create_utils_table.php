<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('email_verified')->nullable();
            $table->boolean('mobile_no_verified')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_no_verified_at')->nullable();
            $table->string('email_verify_token')->nullable();
            $table->string('mobile_no_verify_token')->nullable();
            $table->string('email_verify_otp')->nullable();
            $table->string('mobile_no_verify_otp')->nullable();
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
        Schema::dropIfExists('utils');
    }
}
