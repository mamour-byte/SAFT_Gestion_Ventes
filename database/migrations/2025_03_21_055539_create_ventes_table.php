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
                    $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

                    $table->unsignedBigInteger('id_client');
                    $table->foreign('id_client')->references('id_client')->on('clients'); 

                    $table->unsignedBigInteger('id_facture');
                    $table->foreign('id_facture')->references('id_facture')->on('facture');
                
                     
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
