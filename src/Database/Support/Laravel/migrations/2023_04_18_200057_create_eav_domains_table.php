<?php

use Drobotik\Eav\Enum\_DOMAIN;
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
        Schema::create(_DOMAIN::table(), function (Blueprint $table) {
            $table->integerIncrements(_DOMAIN::ID->column());
            $table->string(_DOMAIN::NAME->column(), 191);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_DOMAIN::table());
    }
};
