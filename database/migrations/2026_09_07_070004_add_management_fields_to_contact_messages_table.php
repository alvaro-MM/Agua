<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convierte los mensajes del formulario en algo gestionable desde el
     * panel: hasta ahora se guardaban y nadie podía volver a mirarlos.
     */
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('status')->default('nuevo')->after('message');
            $table->text('internal_notes')->nullable()->after('status');
            $table->foreignId('handled_by')->nullable()->after('internal_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable()->after('handled_by');
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['handled_by']);
            $table->dropIndex(['status']);
            $table->dropSoftDeletes();
            $table->dropColumn(['status', 'internal_notes', 'handled_by', 'handled_at']);
        });
    }
};
