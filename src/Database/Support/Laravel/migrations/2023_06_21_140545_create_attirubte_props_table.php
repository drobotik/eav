<?php

use Drobotik\Eav\Enum\_ATTR_PROP;
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
        Schema::create(_ATTR_PROP::table(), function (Blueprint $table) {
            $table->integerIncrements(_ATTR_PROP::KEY->column());
            $table->unsignedInteger(_ATTR_PROP::ATTRIBUTE_KEY->column())->index();
            $table->string(_ATTR_PROP::NAME->column(), 191);
            $table->string(_ATTR_PROP::VALUE->column(), 191);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(_ATTR_PROP::table());
    }
};
