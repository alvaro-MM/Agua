<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Vuelca el contenido de config/site.php a la base de datos.
 *
 * Es la semilla de arranque de un entorno nuevo: a partir de aquí el contenido
 * lo edita Miguel en /admin y este seeder ya no se vuelve a ejecutar. Es
 * idempotente (usa updateOrCreate sobre el slug), así que relanzarlo restaura
 * los textos originales sin duplicar filas.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedServices();
        $this->seedProducts();
        $this->seedProjects();
    }

    private function seedSettings(): void
    {
        $company = config('site.company');
        $contact = config('site.contact');
        $social = config('site.social');

        SiteSettings::query()->updateOrCreate(
            ['id' => SiteSettings::query()->value('id') ?? 1],
            [
                'company_name' => $company['name'],
                'legal_name' => $company['legal_name'],
                'nif' => $company['nif'],
                'tagline' => $company['tagline'],
                'description' => $company['description'],
                'founded_year' => $company['founded_year'],
                'city' => $company['city'],
                'service_areas' => $company['service_areas'],

                'phone' => $contact['phone'],
                'phone_link' => $contact['phone_link'],
                'whatsapp' => $contact['whatsapp'],
                'whatsapp_message' => $contact['whatsapp_message'],
                'email' => $contact['email'],
                'notify_email' => $contact['notify_email'],
                'address' => $contact['address'],
                'postal_code' => $contact['postal_code'],
                'schedule' => $contact['schedule'],
                'schedule_short' => $contact['schedule_short'],
                'maps_embed' => $contact['maps_embed'],

                'facebook' => $social['facebook'],
                'instagram' => $social['instagram'],
            ]
        );
    }

    private function seedServices(): void
    {
        foreach (config('site.services') as $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'title' => $service['title'],
                    'excerpt' => $service['excerpt'],
                    'description' => $service['description'],
                    'icon' => $service['icon'],
                    'features' => $service['features'],
                    'is_published' => true,
                    // Los tres salen hoy en la portada.
                    'is_featured' => true,
                ]
            );
        }
    }

    private function seedProducts(): void
    {
        foreach (config('site.catalog') as $product) {
            Product::query()->updateOrCreate(
                ['slug' => Str::slug($product['name'])],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'description' => $product['description'],
                    'image_path' => $product['image'],
                    'is_published' => true,
                    'is_featured' => false,
                ]
            );
        }
    }

    private function seedProjects(): void
    {
        foreach (config('site.projects') as $project) {
            Project::query()->updateOrCreate(
                ['slug' => Str::slug($project['title'])],
                [
                    'title' => $project['title'],
                    'location' => $project['location'],
                    'description' => $project['description'],
                    'image_path' => $project['image'],
                    'is_published' => true,
                    // Los tres salen hoy en la portada.
                    'is_featured' => true,
                ]
            );
        }
    }
}
