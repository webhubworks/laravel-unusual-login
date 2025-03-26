<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_login_attempts', function (Blueprint $table) {
            $table->id();

            $table->string('identifier')->comment('What the user logs in with: email, username, etc.');
            $table->integer('attempts')->default(0);

            $table->timestamps();
        });
    }
};
