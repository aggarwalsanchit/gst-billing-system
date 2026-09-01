// database/migrations/xxxx_create_bill_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('demo', function (Blueprint $table) {
            $table->id('demo_id'); // Matches your demo_id
            $table->string('bill_id', 100);
            $table->string('Product', 1000);
            $table->string('pnumber', 100)->nullable();
            $table->integer('qty')->default(0);
            $table->string('unit', 100)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('nsn_code', 100)->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('database_id', 100)->nullable();
            $table->string('product_id', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('demo');
    }
};