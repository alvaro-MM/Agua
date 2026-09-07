<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajustes del sitio: una única fila con columnas tipadas. El conjunto de
     * campos es fijo y conocido, así que se valida mejor que un key/value
     * genérico y Filament lo pinta como un formulario normal.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Empresa
            $table->string('company_name')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('nif')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('city')->nullable();
            $table->json('service_areas')->nullable();

            // Contacto
            $table->string('phone')->nullable();
            $table->string('phone_link')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('whatsapp_message')->nullable();
            $table->string('email')->nullable();
            $table->string('notify_email')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('schedule')->nullable();
            $table->string('schedule_short')->nullable();
            $table->text('maps_embed')->nullable();

            // Redes
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
