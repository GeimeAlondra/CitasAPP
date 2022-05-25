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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            //Llave foranea de user
            $table->foreignId('user_id')
            ->nullable()
            ->constrained('users')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            //Campo fecha
            $table->date('fecha');
            //Campo estado
            $table->string('estado',1)->default('R');
            //Llave foranea de horario
            $table->foreignId('horario_id')
            ->nullable()
            ->constrained('horarios')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            $table->foreignId('dentista_id')
            ->nullable()
            ->constrained('dentistas')
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
        Schema::dropIfExists('citas');
    }
};
