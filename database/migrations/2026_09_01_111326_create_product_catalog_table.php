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
        Schema::create('product_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('product_no', 50)->unique();
            $table->string('name', 255);
            $table->decimal('rate', 10, 2);
            $table->string('size', 50);
            $table->string('work', 255);
            $table->string('design', 255);
            $table->string('material', 255);
            $table->string('colours', 255);
            $table->string('image_path', 500)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for faster searching
            $table->index('product_no');
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_catalog');
    }
};