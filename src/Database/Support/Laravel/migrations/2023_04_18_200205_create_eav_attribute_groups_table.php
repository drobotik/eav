<?php

use Drobotik\Eav\Enum\_GROUP;
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
        Schema::create(_GROUP::table(), function (Blueprint $table) {
            $table->integerIncrements(_GROUP::ID->column());
            $table->unsignedInteger(_GROUP::SET_ID->column())->index();
            $table->string(_GROUP::NAME->column(), 191);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_GROUP::table());
    }
};
