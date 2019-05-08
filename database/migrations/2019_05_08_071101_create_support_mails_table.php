<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupportMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_mails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('assigned_to')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('contact')->nullable();
            $table->text('subject');
            $table->text('message');
            $table->smallInteger('status')->default(0)->comment('0: Unsolved, 1:Pending, 3: Solved');
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
        Schema::dropIfExists('support_mails');
    }
}
