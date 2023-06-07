<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido', function (Blueprint $table) {
            $table->increments('idPedido');
            $table->unsignedInteger('idProducto');
            $table->string('nombre', 150);
            $table->integer('cantidad');
            $table->date('fecha');
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->unsignedInteger('idCliente');
            
            $table->foreign('idProducto')->references('idProducto')->on('producto');
            $table->foreign('idCliente')->references('idCliente')->on('cliente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido');
    }
};
