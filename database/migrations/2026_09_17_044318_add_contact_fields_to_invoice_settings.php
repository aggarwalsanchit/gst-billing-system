<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_settings', 'tagline')) {
                $table->string('tagline')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('invoice_settings', 'company_phone_2')) {
                $table->string('company_phone_2')->nullable()->after('company_phone');
            }
            if (!Schema::hasColumn('invoice_settings', 'company_email')) {
                $table->string('company_email')->nullable()->after('company_phone_2');
            }
            if (!Schema::hasColumn('invoice_settings', 'company_website')) {
                $table->string('company_website')->nullable()->after('company_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'company_phone_2',
                'company_email',
                'company_website',
            ]);
        });
    }
};
