<?php

use Drobotik\Eav\Enum\_PIVOT;
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
        Schema::create(_PIVOT::table(), function (Blueprint $table) {
            $table->integerIncrements(_PIVOT::ID->column());
            $table->unsignedInteger(_PIVOT::DOMAIN_ID->column())->index();
            $table->unsignedInteger(_PIVOT::SET_ID->column())->index();
            $table->unsignedInteger(_PIVOT::GROUP_ID->column())->index();
            $table->unsignedInteger(_PIVOT::ATTR_ID->column())->index();
            $table->unique([
                _PIVOT::DOMAIN_ID->column(),
                _PIVOT::SET_ID->column(),
                _PIVOT::GROUP_ID->column(),
                _PIVOT::ATTR_ID->column(),
            ], sprintf('%s_unique', _PIVOT::table()));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_PIVOT::table());
    }
};
