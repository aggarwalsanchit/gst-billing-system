// database/migrations/xxxx_create_bills_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('billdate', function (Blueprint $table) {
            $table->id('dateid'); // Matches your dateid
            $table->string('bill_id', 100)->unique();
            $table->string('customer_id', 100);
            $table->date('bill_date');
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->string('size', 255)->nullable()->default('490');
            $table->decimal('transport', 10, 2)->default(0);
            $table->decimal('package', 10, 2)->default(0);
            $table->timestamps();
            
            // Foreign key (will add after migration)
        });
    }

    public function down()
    {
        Schema::dropIfExists('billdate');
    }
};