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
        if (!Schema::hasTable("product_variant")) {
            Schema::create('product_variant', function (Blueprint $table) {
                $table->id("id");
                $table->double("price")->nullable();
                $table->double("sale_price")->nullable();
                $table->integer("product_id");
                $table->integer("product_parent")->nullable();
                $table->integer("quantity")->unsigned();
                $table->integer("created_at");
                $table->integer("updated_at");
                $table->enum("active", ["Y", "N"]);
                $table->text("image")->nullable();
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
        Schema::dropIfExists('product_variant');
    }
};
