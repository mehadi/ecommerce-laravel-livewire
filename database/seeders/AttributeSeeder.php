<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Weight attribute
        $weight = Attribute::firstOrCreate(
            ['slug' => 'weight'],
            [
                'name' => 'Weight',
                'type' => 'decimal',
                'unit' => 'kg',
                'is_required' => false,
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Create default weight values
        if ($weight->values()->count() === 0) {
            $weightValues = ['0.5', '1', '1.5', '2', '2.5', '3', '5'];
            foreach ($weightValues as $index => $value) {
                AttributeValue::create([
                    'attribute_id' => $weight->id,
                    'value' => $value,
                    'display_value' => $value.' kg',
                    'order' => $index,
                    'is_active' => true,
                ]);
            }
        }

        // Create Color attribute
        $color = Attribute::firstOrCreate(
            ['slug' => 'color'],
            [
                'name' => 'Color',
                'type' => 'text',
                'unit' => null,
                'is_required' => false,
                'order' => 2,
                'is_active' => true,
            ]
        );

        // Create default color values
        if ($color->values()->count() === 0) {
            $colorValues = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow'];
            foreach ($colorValues as $index => $value) {
                AttributeValue::create([
                    'attribute_id' => $color->id,
                    'value' => $value,
                    'display_value' => null,
                    'order' => $index,
                    'is_active' => true,
                ]);
            }
        }

        // Create Size attribute
        $size = Attribute::firstOrCreate(
            ['slug' => 'size'],
            [
                'name' => 'Size',
                'type' => 'text',
                'unit' => null,
                'is_required' => false,
                'order' => 3,
                'is_active' => true,
            ]
        );

        // Create default size values
        if ($size->values()->count() === 0) {
            $sizeValues = ['Small', 'Medium', 'Large', 'XL', 'XXL'];
            foreach ($sizeValues as $index => $value) {
                AttributeValue::create([
                    'attribute_id' => $size->id,
                    'value' => $value,
                    'display_value' => null,
                    'order' => $index,
                    'is_active' => true,
                ]);
            }
        }
    }
}
