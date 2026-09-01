// database/migrations/xxxx_create_notes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('note', function (Blueprint $table) {
            $table->id('note_id'); // Matches your note_id
            $table->string('despatch', 1000);
            $table->string('bill_id', 100);
            $table->string('customer_id', 100);
            $table->date('bill_date');
            $table->string('deliverynote', 100);
            $table->string('grno', 100)->nullable()->default('10');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('note');
    }
};