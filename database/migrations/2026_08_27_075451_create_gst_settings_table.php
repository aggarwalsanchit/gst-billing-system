// database/migrations/xxxx_create_gst_settings_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gst', function (Blueprint $table) {
            $table->id('gid');
            $table->decimal('gst1', 10, 2)->default(5);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gst');
    }
};