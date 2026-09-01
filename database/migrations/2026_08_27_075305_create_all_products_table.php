// database/migrations/xxxx_create_all_products_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('allproducts', function (Blueprint $table) {
            $table->id('product_id'); // Matches your product_id
            $table->string('name', 100);
            $table->string('pnumber', 100);
            $table->string('unit', 100);
            $table->decimal('price', 10, 2);
            $table->string('hsn_code', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('allproducts');
    }
};