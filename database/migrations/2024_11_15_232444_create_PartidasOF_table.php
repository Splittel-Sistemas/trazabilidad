<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasOFTable extends Migration
{
    public function up()
    {
        Schema::create('partidas_of', function (Blueprint $table) { 
            $table->id();

            
            $table->foreignId('orden_fabricacion_id') 
                ->constrained('orden_fabricacion') 
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->integer('cantidad_partida');
            $table->date('fecha_fabricacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partidas_of');
    }
}
