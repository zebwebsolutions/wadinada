<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('imei1')->nullable()->unique()->after('sku');
            $table->string('imei2')->nullable()->unique()->after('imei1');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('kuwait_id_path')->nullable()->after('kuwait_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['imei1']);
            $table->dropUnique(['imei2']);
            $table->dropColumn(['imei1', 'imei2']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('kuwait_id_path');
        });
    }
};
