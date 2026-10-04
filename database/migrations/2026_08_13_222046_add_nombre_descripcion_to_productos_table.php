<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Solo agrega cada columna si todavía no existe
     * (create_productos_table ya las incluye en bases nuevas).
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'nombre')) {
                $table->string('nombre');
            }
            if (!Schema::hasColumn('productos', 'descripcion')) {
                $table->string('descripcion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};