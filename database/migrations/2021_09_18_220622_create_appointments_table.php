<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('guests')->onUpdate('cascade')->onDelete('cascade');
            $table->string('purpose');
            $table->enum('status',['waiting','accepted','declined'])->default('waiting');
            $table->string('notes')->nullable();
            $table->string('date');
            $table->time('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
