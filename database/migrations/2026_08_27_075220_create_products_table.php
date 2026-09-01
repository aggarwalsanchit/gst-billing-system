// database/migrations/xxxx_create_products_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id('database_id'); // Matches your database_id
            $table->string('name', 100);
            $table->string('pnumber', 100)->nullable();
            $table->integer('qty')->default(0);
            $table->string('unit', 100)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('nsn_code', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product');
    }
};