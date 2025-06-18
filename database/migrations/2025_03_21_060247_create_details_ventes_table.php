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
    Schema::create('details_ventes', function (Blueprint $table) {
        $table->id('id_details_ventes'); 

        $table->unsignedBigInteger('id_product'); 
        $table->foreign('id_product')->references('id_product')->on('products'); 

        $table->unsignedBigInteger('id_vente'); 
        $table->foreign('id_vente')->references('id_vente')->on('ventes');

        $table->date('date_vente')->nullable(); 
        $table->decimal('prix_total', 10, 2);
        
        $table->integer('quantite_vendue');

        $table->string('NumeroCommande')->nullable();

        $table->string('numeroBonLivraison')->nullable();

        $table->string('DateLivraison');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details_ventes');
    }
};
