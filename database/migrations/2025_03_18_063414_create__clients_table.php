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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id_client');
            $table->string('nom', 100); 
            $table->string('email')->unique(); 
            $table->string('telephone')->nullable(); 
            $table->text('adresse')->nullable(); 
            $table->string('NumeroNinea')->nullable();
            $table->string('NumeroRegistreCommerce')->nullable();
            $table->boolean('archived')->default(false); 
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_clients');
    }
};
