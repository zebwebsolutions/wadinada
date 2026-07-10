<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('kuwait_id_front_path')->nullable()->after('kuwait_id');
            $table->string('kuwait_id_back_path')->nullable()->after('kuwait_id_front_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['kuwait_id_front_path', 'kuwait_id_back_path']);
        });
    }
};
