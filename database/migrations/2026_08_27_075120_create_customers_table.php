// database/migrations/xxxx_create_customers_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id('customer_id'); // Matches your customer_id
            $table->string('name', 100);
            $table->string('address', 1000)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('gstnumber', 100)->nullable();
            $table->string('adharno', 1000)->nullable()->default('0');
            $table->string('panno', 1000)->nullable()->default('0');
            $table->string('state', 255)->nullable()->default('0');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer');
    }
};