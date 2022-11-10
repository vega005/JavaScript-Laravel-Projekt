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
        Schema::create('files', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Date');
            $table->string('Time');
            $table->string('Duration');
            $table->string('LCRNo');
            $table->string('External_partner');
            $table->string('External_name');
            $table->string('Scr_no_invoice');
            $table->string('Scr_name_invoice');
            $table->string('Scr_no_real');
            $table->string('Scr_name_real');
            $table->string('Connection_no');
            $table->string('Charges');
            $table->string('Direction');
            $table->string('Bill_type');
            $table->string('Call_type');
            $table->string('Proj');
            $table->string('HotId');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');

    }
};
