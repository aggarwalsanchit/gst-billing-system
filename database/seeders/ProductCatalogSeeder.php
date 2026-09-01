<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCatalog;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'product_no' => '2162',
                'name' => 'SHAWL NO. 2162',
                'rate' => 82.00,
                'size' => '30"/64" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'GOLDEN DESIGN - ALL PLAIN BORDER BUTY',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => 'WHITE ONLY',
                'description' => 'ALL PLAIN BORDER BUTY MATERIAL',
                'is_active' => true
            ],
            [
                'product_no' => '2181A',
                'name' => 'SHAWL NO. 2181A',
                'rate' => 98.00,
                'size' => '34"/72" APROX',
                'work' => 'BLOCK PRINTING WITH BUMBLE DESIGN',
                'design' => 'ALL PLAIN KUNJ BORDER BUTY',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN KUNJ BORDER BUTY MATERIAL',
                'is_active' => true
            ],
            [
                'product_no' => '2184',
                'name' => 'SHAWL NO. 2184',
                'rate' => 98.00,
                'size' => '32"/66" APROX',
                'work' => 'BLOCK PRINTING WITH BUMBLE DESIGN',
                'design' => 'SELF BINDI CONTRAST PALLA KUNJ BORDER',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => '4-5 LIGHT',
                'description' => 'SELF BINDI CONTRAST PALLA KUNJ BORDER',
                'is_active' => true
            ],
            [
                'product_no' => '2181B',
                'name' => 'SHAWL NO. 2181B',
                'rate' => 98.00,
                'size' => '34"/72" APROX',
                'work' => 'BLOCK PRINTING WITH BUMBLE DESIGN',
                'design' => 'ALL PLAIN- BORDER VARIETY JAL',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN- BORDER VARIETY JAL MATERIAL',
                'is_active' => true
            ],
            [
                'product_no' => '2191',
                'name' => 'SHAWL NO. 2191',
                'rate' => 87.00,
                'size' => '34"/70" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'SELF CHESS KUNJ BORDER BUTI',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => '4-5 LIGHT',
                'description' => 'SELF CHESS KUNJ BORDER BUTI',
                'is_active' => true
            ],
            [
                'product_no' => '2201',
                'name' => 'SHAWL NO. 2201',
                'rate' => 104.00,
                'size' => '34"/72" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'ALL PLAIN - KUNJ BORDER BUTI',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN - KUNJ BORDER BUTI',
                'is_active' => true
            ],
            [
                'product_no' => '2203',
                'name' => 'SHAWL NO. 2203',
                'rate' => 107.00,
                'size' => '35"/72" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'BORDER KUNJ',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '(4 - 5)LIGHT',
                'description' => 'BORDER KUNJ MATERIAL',
                'is_active' => true
            ],
            [
                'product_no' => '2223',
                'name' => 'SHAWL NO. 2223',
                'rate' => 130.00,
                'size' => '36"/72" APROX',
                'work' => 'MULTY COLOUR PRINTING',
                'design' => 'ALL PLAIN - PALLA VARIETY JAL',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN - PALLA VARIETY JAL',
                'is_active' => true
            ],
            [
                'product_no' => '2241',
                'name' => 'SHAWL NO. 2241',
                'rate' => 71.00,
                'size' => '36"/72" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'SELF CONTRAST PALLA BORDER JAL',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => '4-5 LIGHT',
                'description' => 'SELF CONTRAST PALLA BORDER JAL',
                'is_active' => true
            ],
            [
                'product_no' => '2251',
                'name' => 'SHAWL NO. 2251',
                'rate' => 113.00,
                'size' => '36"/78" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'ALL PLAIN BORDER VARIETY JAL',
                'material' => 'POLY ACRYLIC WOOL',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN BORDER VARIETY JAL',
                'is_active' => true
            ],
            [
                'product_no' => '2252',
                'name' => 'SHAWL NO. 2252',
                'rate' => 115.00,
                'size' => '36"/72" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'SELF BINDI BORDER - KUNJ BORDER BUTI',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => '4-5 LIGHT & WHITE',
                'description' => 'SELF BINDI BORDER - KUNJ BORDER BUTI',
                'is_active' => true
            ],
            [
                'product_no' => '2254',
                'name' => 'SHAWL NO. 2254',
                'rate' => 119.00,
                'size' => '38"/76" APROX',
                'work' => 'BLOCK PRINT WITH BUMBLE DESIGN',
                'design' => 'SELF CONTRAST PALLA - KUNJ BORDER BUTY',
                'material' => 'ROTOR SPUN WOOL',
                'colours' => '4-5 LIGHT',
                'description' => 'SELF CONTRAST PALLA - KUNJ BORDER BUTY',
                'is_active' => true
            ],
            [
                'product_no' => '2255',
                'name' => 'SHAWL NO. 2255',
                'rate' => 131.00,
                'size' => '36"/80" APROX',
                'work' => 'BLOCK PRINT WITH BUMBLE DESIGN',
                'design' => 'ALL PLAIN - KUNJ BORDER BUTY',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN - KUNJ BORDER BUTY',
                'is_active' => true
            ],
            [
                'product_no' => '2256B',
                'name' => 'SHAWL NO. 2256B',
                'rate' => 120.00,
                'size' => '36"/80" APROX',
                'work' => 'BLOCK PRINT WITH BUMBLE DESIGN',
                'design' => 'ALL PLAIN - KUNJ BORDER BUTY',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN - KUNJ BORDER BUTY',
                'is_active' => true
            ],
            [
                'product_no' => '2256C',
                'name' => 'SHAWL NO. 2256C',
                'rate' => 120.00,
                'size' => '36"/80" APROX',
                'work' => 'BLOCK PRINT WITH BUMBLE DESIGN',
                'design' => 'ALL PLAIN - KUNJ BORDER BUTY',
                'material' => '2/48 PP WOOL (VERY FINE)',
                'colours' => '4-5 LIGHT',
                'description' => 'ALL PLAIN - KUNJ BORDER BUTY',
                'is_active' => true
            ],
            [
                'product_no' => '2282',
                'name' => 'SHAWL NO. 2282',
                'rate' => 128.00,
                'size' => '40"/80" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'BIG BORDER WITH KUNJ BUTY',
                'material' => 'ACRYLIC WOOL',
                'colours' => '4 - 5 LIGHT',
                'description' => 'BIG BORDER WITH KUNJ BUTY',
                'is_active' => true
            ],
            [
                'product_no' => '2291',
                'name' => 'SHAWL NO. 2291',
                'rate' => 120.00,
                'size' => '40"/80" APROX',
                'work' => 'BLOCK PRINTING',
                'design' => 'ALL PLAIN - KUNJ BORDER AMBI JAL',
                'material' => 'POLY ACRYLIC WOOL',
                'colours' => '4-6 LIGHT',
                'description' => 'ALL PLAIN - KUNJ BORDER AMBI JAL',
                'is_active' => true
            ],
        ];

        foreach ($products as $product) {
            ProductCatalog::create($product);
        }

        $this->command->info('Product catalog seeded successfully!');
        $this->command->info('Total products: ' . count($products));
    }
}