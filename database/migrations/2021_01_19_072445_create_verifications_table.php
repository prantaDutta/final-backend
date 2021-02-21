<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->date('date_of_birth');
            $table->enum('gender',['male','female']);
            $table->string('address');
//            $table->bigInteger('mobile_no');
//            $table->timestamp('mobile_no_verified_at')->nullable();
//            $table->enum('document_type',['nid','passport']);
            $table->enum('borrower_type',['salaried','self'])->nullable();
            $table->string('division');
            $table->string('zila');
            $table->integer('zip_code');
            $table->json('verification_photos');
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
        Schema::dropIfExists('verifications');
    }
}
