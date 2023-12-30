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
        Schema::table('category', function (Blueprint $table) {
            $table->integer("order")->default(0)->after("image");
            $table->integer("parent_category")->default(0)->change();
            $table->integer("created_at")->change();
            $table->integer("updated_at")->change();
            $table->enum("active", ["Y", "N"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->dropColumn("order");
            $table->dropColumn("parent_category");
            $table->timestamp("created_at")->change();
            $table->timestamp("updated_at")->change();
            $table->dropColumn("active");
        });
    }
};
