<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create 'universities' table
        if (!Schema::hasTable('universities')) Schema::create('universities', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });
        // Create 'tickets' table
        if (!Schema::hasTable('tickets')) Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->text('email')->unique();
            $table->text('password');
            $table->text('name');
            $table->unsignedInteger('university_id');
            $table->foreign('university_id')->references('id')->on('universities');
            $table->boolean('binusian')->default(false);
            $table->bigInteger('nim');
            $table->text('phone');
            $table->text('line')->nullable();
            $table->text('whatsapp')->nullable();
        });
        // Create 'events' table
        if (!Schema::hasTable('events')) Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('location')->default('Online');
            $table->dateTime('date', 0);
            $table->boolean('opened')->default(false);
            $table->boolean('attendance_opened')->default(false);
            $table->boolean('attendance_is_exit')->default(false);
            $table->text('url_link')->nullable();
            $table->text('totp_key');
            $table->unsignedInteger('seats')->default(0);
        });
        // Create 'teams' table
        if (!Schema::hasTable('teams')) Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->integer('score');
            $table->text('remarks');
        });
        // Create 'registration' table
        if (!Schema::hasTable('registration')) Schema::create('registration', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->foreign('ticket_id')->references('id')->on('tickets');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->unsignedInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->integer('status');
            $table->text('remarks');
        });
        // Create 'attendance' table
        if (!Schema::hasTable('attendance')) Schema::create('attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('timestamp');
            $table->unsignedInteger('registration_id');
            $table->foreign('registration_id')->references('id')->on('registration');
            $table->text('type');
        });
        // Add BINUS
        DB::table('universities')->insert(['name' => 'BINUS University']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('registration');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('events');
        Schema::dropIfExists('universities');
    }
}