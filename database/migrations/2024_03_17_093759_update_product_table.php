<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn("products", "brand_id")) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer("brand_id")->after("description");
            });
        }

        if (!Schema::hasColumn("products", "brand_id")) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer("order")->default(1)->after("description");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn("products", "brand_id")) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn("brand_id");
            });
        }

        if (Schema::hasColumn("products", "brand_id")) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn("order");
            });
        }
    }
};
