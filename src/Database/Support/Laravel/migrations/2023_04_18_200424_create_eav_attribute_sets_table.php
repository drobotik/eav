<?php

use Drobotik\Eav\Enum\_SET;
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
        Schema::create(_SET::table(), function (Blueprint $table) {
            $table->integerIncrements(_SET::ID->column());
            $table->unsignedInteger(_SET::DOMAIN_ID->column());
            $table->string(_SET::NAME->column());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_SET::table());
    }
};
