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
        Schema::create('custom_lists', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nome della lista (es. "Wishlist", "Comfort Movies")
            $table->string('description')->nullable(); // Descrizione opzionale
            $table->string('type')->default('custom'); // 'wishlist' per quella speciale, 'custom' per le altre
            $table->boolean('is_public')->default(false); // Pubblica o Privata
            $table->json('anime_ids')->nullable(); // Array JSON degli ID anime inclusi [123, 456, 789]
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_lists');
    }
};
