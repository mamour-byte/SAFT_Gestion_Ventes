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
        Schema::create('facture', function (Blueprint $table) {
            $table->id('id_facture');

            $table->unsignedBigInteger('id_client');
            $table->foreign('id_client')->references('id_client')->on('clients');
            
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('statut');
            $table->timestamps();

            $table->string('numero_facture')->nullable()->unique();
            
            $table->boolean('tva')->default(false);

            $table->enum('type_document', ['facture', 'devis','avoir'])->default('facture');

            $table->string('reference_facture')->nullable(); // l’avoir fait référence à une facture
            $table->string('reference_avoir')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture');
    }
};
