<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreateMailingsTable extends Migration
{
    use SoftDeletes;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status');
            $table->text('mailing_name')->nullable();
            $table->text('mode');
            $table->text('subject')->nullable();
            $table->text('email_address')->nullable();
            $table->text('list_of_emails')->nullable();
            $table->text('sent_log')->nullable();
            $table->text('error_log')->nullable();
            $table->timestamp('sended_at')->nullable();
            $table->text('greetings')->nullable();
            $table->text('message')->nullable();
            $table->text('sender');
            $table->text('signature')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('mailings');
    }
}
