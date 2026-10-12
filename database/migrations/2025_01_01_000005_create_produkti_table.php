<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produkti', function (Blueprint $table) {
            $table->id('produkta_id');
            $table->foreignId('kategorijas_id')
                ->constrained('kategorijas', 'kategorijas_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nosaukums', 255);
            $table->text('apraksts')->nullable();
            $table->decimal('cena', 8, 2);
            $table->string('attels', 255)->nullable();
            $table->integer('krajums')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produkti');
    }
};
