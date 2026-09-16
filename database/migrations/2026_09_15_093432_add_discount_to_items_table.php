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
        Schema::table('demo', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->after('price');
            $table->decimal('net_total', 12, 2)->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demo', function (Blueprint $table) {
            $table->dropColumn(['discount', 'net_total']);
        });
    }
};
