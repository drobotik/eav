<?php

use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
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
        Schema::create(ATTR_TYPE::INTEGER->valueTable(), function (Blueprint $table) {
            $table->integerIncrements(_VALUE::ID->column());
            $table->unsignedInteger(_VALUE::DOMAIN_ID->column())->index();
            $table->unsignedInteger(_VALUE::ENTITY_ID->column())->index();
            $table->unsignedInteger(_VALUE::ATTRIBUTE_ID->column())->index();
            $table->integer(_VALUE::VALUE->column())->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ATTR_TYPE::INTEGER->valueTable());
    }
};
