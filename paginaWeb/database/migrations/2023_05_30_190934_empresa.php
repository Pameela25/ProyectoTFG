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
        Schema::create('empresa', function (Blueprint $table) {
            $table->increments('idEmpresa');
            $table->string('nombre', 150)->nullable();
            $table->string('tipo', 120)->nullable();
            $table->string('telefono', 12)->nullable();
            $table->binary('documentacionCliente');
            $table->binary('documentacionProducto');
            // Hace una relación con la tabla 'users'
            $table->unsignedBigInteger('idUsuario');
            $table->foreign('idUsuario')->references('id')->on('users');
            //$table->withoutTimestamps(); // Desactiva las columnas de marca de tiempo

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa');
    }
};
