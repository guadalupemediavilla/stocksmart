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
        Schema::table('stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stocks', 'anulado_at')) {
                $table->timestamp('anulado_at')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'anulado_por')) {
                $table->foreignId('anulado_por')->nullable()->constrained('empleados');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (Schema::hasColumn('stocks', 'anulado_por')) {
                $table->dropForeign(['anulado_por']);
                $table->dropColumn('anulado_por');
            }
            if (Schema::hasColumn('stocks', 'anulado_at')) {
                $table->dropColumn('anulado_at');
            }
        });
    }
};
