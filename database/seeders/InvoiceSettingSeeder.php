<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoiceSetting;

class InvoiceSettingSeeder extends Seeder
{
    public function run()
    {
        InvoiceSetting::createDefault();
    }
}