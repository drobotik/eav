<?php

use Drobotik\Eav\Enum\_ENTITY;
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
        Schema::create(_ENTITY::table(), function (Blueprint $table) {
            $table->integerIncrements(_ENTITY::ID->column());
            $table->unsignedInteger(_ENTITY::DOMAIN_ID->column())->index();
            $table->unsignedInteger(_ENTITY::ATTR_SET_ID->column())->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_ENTITY::table());
    }
};
