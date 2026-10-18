<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lietotaji', function (Blueprint $table) {
            $table->string('adrese', 255)->nullable()->after('e_pasts');
        });
    }

    public function down(): void
    {
        Schema::table('lietotaji', function (Blueprint $table) {
            $table->dropColumn('adrese');
        });
    }
};
