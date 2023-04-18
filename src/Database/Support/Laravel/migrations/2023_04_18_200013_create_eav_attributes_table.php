<?php

use Drobotik\Eav\Enum\_ATTR;
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
        Schema::create(_ATTR::table(), function (Blueprint $table) {
            $table->integerIncrements(_ATTR::ID->column());
            $table->unsignedInteger(_ATTR::DOMAIN_ID->column())->index();
            $table->string(_ATTR::NAME->column(), 191);
            $table->string(_ATTR::TYPE->column(), 191);
            $table->string(_ATTR::STRATEGY->column(), 191)->nullable();
            $table->string(_ATTR::SOURCE->column(), 191)->nullable();
            $table->string(_ATTR::DEFAULT_VALUE->column(), 191)->nullable();
            $table->text(_ATTR::DESCRIPTION->column())->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_ATTR::table());
    }
};
