<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de los platos de la carta del Bar Baio.
 * php artisan migrate  (crear)  ·  php artisan migrate:rollback  (deshacer)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->enum('tipo', ['entrante', 'principal', 'postre']);
            $table->decimal('precio', 6, 2);
            $table->boolean('vegano')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platos');
    }
};
