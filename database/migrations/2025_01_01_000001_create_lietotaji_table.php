<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lietotaji', function (Blueprint $table) {
            $table->id('lietotaja_id');
            $table->string('vards', 30);
            $table->string('uzvards', 30);
            $table->string('name', 255)->nullable();
            $table->string('e_pasts', 30)->unique();
            $table->string('email', 255)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('parole', 255);
            $table->string('password', 255)->nullable();
            $table->rememberToken();
            $table->tinyInteger('loma')->default(0); // 0 - pircējs, 1 - administrators
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lietotaji');
    }
};
