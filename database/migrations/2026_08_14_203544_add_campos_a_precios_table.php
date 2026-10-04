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
        Schema::table('precios', function (Blueprint $table) {
            $table->foreignId('id_variante')->constrained('variantes');
$table->foreignId('id_empleado')->constrained('empleados');
$table->float('precio');
$table->date('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('precios', function (Blueprint $table) {
            //
        });
    }
};
