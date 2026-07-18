<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('navbar_components', function (Blueprint $table) {
            $table->string('zone_desktop')->default('middle')->after('key');
        });

        $zones = [
            'logo' => 'start',
            'search' => 'middle',
            'nav_links' => 'middle',
            'categories' => 'middle',
            'cart_button' => 'end',
            'wishlist_button' => 'end',
            'language_switcher' => 'end',
        ];

        foreach ($zones as $key => $zone) {
            DB::table('navbar_components')->where('key', $key)->update(['zone_desktop' => $zone]);
        }
    }

    public function down(): void
    {
        Schema::table('navbar_components', function (Blueprint $table) {
            $table->dropColumn('zone_desktop');
        });
    }
};
