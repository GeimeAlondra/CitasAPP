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
        Schema::create('detalle_servicios', function (Blueprint $table) {
            $table->id();
            //Llave foranea de servicio
            $table->foreignId('servicio_id')
            ->nullable()
            ->constrained('servicios')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            //Llave foranea de cita
            $table->foreignId('cita_id')
            ->nullable()
            ->constrained('citas')
            ->cascadeOnUpdate()
            ->nullOnDelete();
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
        Schema::dropIfExists('detalle_servicios');
    }
};
