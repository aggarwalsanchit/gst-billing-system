<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            
            // Company Details
            $table->string('company_name')->default('A.B SHAWLS');
            $table->string('company_address')->default('10, Kamla Market, Shastri Market, Amritsar, Punjab');
            $table->string('company_gst')->default('03AEIPA3719A1ZD');
            $table->string('company_phone')->default('+91-9417807792');
            $table->string('company_phone2')->default('+91-6280845993');
            $table->string('company_logo')->nullable();
            
            // Bank Details
            $table->string('bank_name')->default('BANK OF BARODA');
            $table->string('bank_account')->default('70940200002257');
            $table->string('bank_ifsc')->default('BARB0DBAMRI');
            $table->string('bank_branch')->nullable();
            
            // Terms & Conditions
            $table->text('terms_conditions')->nullable();
            
            // Default terms as array
            $table->json('terms_list')->nullable();
            
            // Footer Text
            $table->text('footer_text')->nullable();
            
            // GST Settings
            $table->decimal('default_gst_rate', 5, 2)->default(5.00);
            
            // Invoice Notes
            $table->text('invoice_notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_settings');
    }
};