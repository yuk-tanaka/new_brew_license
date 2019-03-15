<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('drink_type_id')->index();
            $table->unsignedInteger('prefecture')->index();
            $table->string('name', 1000);
            $table->string('address', 255);
            $table->boolean('can_send_notification')->default(true);
            $table->timestamp('permitted_at')->index();
            $table->timestamps();

            $table->foreign('drink_type_id')->references('id')->on('drink_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licenses');
    }
}
