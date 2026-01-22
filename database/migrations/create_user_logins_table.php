<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique();
            // ⚠️ Check, if you should better use one of those as reference to the users table:
            // $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            // $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('ip_address');
            $table->string('user_agent');

            $table->timestamps();
        });
    }
};
