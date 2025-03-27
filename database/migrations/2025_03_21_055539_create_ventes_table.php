<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
            {
                Schema::create('ventes', function (Blueprint $table) {
                    $table->id('id_vente'); 

                    $table->unsignedBigInteger('id_user'); 
                    $table->foreign('id_user')->references('id')->on('users');

                    $table->unsignedBigInteger('id_client');
                    $table->foreign('id_client')->references('id_client')->on('clients'); 

                    $table->unsignedBigInteger('id_product');
                    $table->foreign('id_product')->references('id_product')->on('products');
                     
                    $table->timestamps(); 
                });
            }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
