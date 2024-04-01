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
        if (Schema::hasTable("products")) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer("created_at")->change();
                $table->integer("updated_at")->change();
                $table->integer("deleted_at")->change();
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
        if (Schema::hasTable("products")) {
            Schema::table('products', function (Blueprint $table) {
                $table->timestamp("created_at")->change();
                $table->timestamp("updated_at")->change();
                $table->timestamp("deleted_at")->change();
            });
        }
    }
};
