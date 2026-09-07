<?php

/*
|--------------------------------------------------------------------------
| Configuración de la web pública
|--------------------------------------------------------------------------
| Semilla inicial del contenido del sitio. Desde que existe el panel de
| gestión, estos datos NO los leen las vistas: se vuelcan a la base de datos
| con `SiteContentSeeder` y a partir de ahí Miguel los edita en /admin.
|
| Sirven para arrancar un entorno nuevo y como copia de referencia del
| contenido original. Los textos son provisionales (placeholder) hasta
| recibir el material real de Miguel.
*/

return [

    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrador'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'company' => [
        'name' => 'Electro Bombas MAPF',
        'legal_name' => 'Electro Bombas MAPF — [Razón social]',
        'nif' => 'B00000000',
        'tagline' => 'Instalación, reparación y mantenimiento de bombas de agua',
        'description' => 'Empresa especializada en instalación, reparación y mantenimiento de bombas de agua para hogares, comunidades, riego e industria. Servicio rápido y profesional.',
        'founded_year' => 2015,
        'city' => 'Tu Ciudad',
        'service_areas' => ['Tu Ciudad', 'Provincia', 'Comarca'],
    ],

    'contact' => [
        'phone' => '+34 600 000 000',
        'phone_link' => '+34600000000',
        'whatsapp' => '34600000000',
        'whatsapp_message' => 'Hola, me gustaría solicitar información sobre sus servicios de bombas de agua.',
        'email' => 'info@bombasdeagua.example',
        'notify_email' => 'info@bombasdeagua.example',
        'address' => 'Calle Ejemplo, 1',
        'postal_code' => '00000',
        'schedule' => 'Lunes a Viernes de 8:00 a 18:00',
        'schedule_short' => 'L-V 8:00-18:00',
        'maps_embed' => null,
    ],

    'social' => [
        'facebook' => null,
        'instagram' => null,
    ],

    'services' => [
        [
            'slug' => 'instalacion',
            'title' => 'Instalación de bombas',
            'excerpt' => 'Instalación profesional de bombas de agua para pozos, depósitos, riego, presión doméstica y sistemas industriales.',
            'icon' => 'wrench',
            'description' => 'Estudiamos tu necesidad y seleccionamos la bomba adecuada según caudal, presión y uso. Realizamos la instalación completa, puesta en marcha y verificación del funcionamiento con todas las garantías.',
            'features' => [
                'Asesoramiento y selección del equipo idóneo',
                'Instalación de bombas sumergibles, de superficie y grupos de presión',
                'Puesta en marcha y pruebas de funcionamiento',
                'Garantía del trabajo realizado',
            ],
        ],
        [
            'slug' => 'reparacion',
            'title' => 'Reparación de bombas',
            'excerpt' => 'Diagnóstico y reparación de averías en todo tipo de bombas de agua con recambios originales.',
            'icon' => 'bolt',
            'description' => 'Localizamos la avería y reparamos tu bomba de agua con rapidez para minimizar el tiempo sin suministro. Trabajamos con recambios de calidad y ofrecemos presupuesto previo sin compromiso.',
            'features' => [
                'Diagnóstico de averías',
                'Reparación con recambios originales',
                'Presupuesto previo sin compromiso',
                'Servicio de urgencia',
            ],
        ],
        [
            'slug' => 'mantenimiento',
            'title' => 'Mantenimiento preventivo',
            'excerpt' => 'Planes de mantenimiento para alargar la vida útil de tus equipos y evitar averías inesperadas.',
            'icon' => 'shield',
            'description' => 'El mantenimiento preventivo evita paradas y prolonga la vida de tus equipos. Revisamos periódicamente el estado de la bomba, cuadros eléctricos y elementos de seguridad.',
            'features' => [
                'Revisiones periódicas programadas',
                'Control de presión y caudal',
                'Revisión de cuadros eléctricos y protecciones',
                'Informe del estado del equipo',
            ],
        ],
    ],

    'catalog' => [
        [
            'name' => 'Bomba sumergible',
            'category' => 'Bombas',
            'description' => 'Ideal para pozos y sondeos. Alta eficiencia y resistencia.',
            'image' => 'https://loremflickr.com/800/600/submersible,water,pump?lock=11',
        ],
        [
            'name' => 'Bomba de superficie',
            'category' => 'Bombas',
            'description' => 'Para trasvase y riego desde depósitos o balsas.',
            'image' => 'https://loremflickr.com/800/600/water,pump,motor?lock=12',
        ],
        [
            'name' => 'Grupo de presión',
            'category' => 'Bombas',
            'description' => 'Mantiene la presión constante del agua en viviendas y comunidades.',
            'image' => 'https://loremflickr.com/800/600/water,pressure,pump?lock=13',
        ],
        [
            'name' => 'Bomba para riego',
            'category' => 'Bombas',
            'description' => 'Diseñada para sistemas de riego agrícola y jardín.',
            'image' => 'https://loremflickr.com/800/600/irrigation,water,sprinkler?lock=14',
        ],
        [
            'name' => 'Depósito de membrana',
            'category' => 'Accesorios',
            'description' => 'Acumula agua a presión y reduce el arranque de la bomba.',
            'image' => 'https://loremflickr.com/800/600/water,tank,steel?lock=15',
        ],
        [
            'name' => 'Recambios y repuestos',
            'category' => 'Accesorios',
            'description' => 'Rodetes, juntas, presostatos, cuadros eléctricos y más.',
            'image' => 'https://loremflickr.com/800/600/tools,pipe,valve?lock=16',
        ],
    ],

    'projects' => [
        [
            'title' => 'Instalación de grupo de presión en comunidad',
            'location' => 'Tu Ciudad',
            'description' => 'Sustitución completa del sistema de presión de agua en un edificio de vecinos.',
            'image' => 'https://loremflickr.com/800/600/water,pump,pipes?lock=21',
        ],
        [
            'title' => 'Bomba sumergible para pozo agrícola',
            'location' => 'Provincia',
            'description' => 'Instalación de bomba sumergible para riego de una finca de cultivo.',
            'image' => 'https://loremflickr.com/800/600/well,water,irrigation?lock=22',
        ],
        [
            'title' => 'Reparación de urgencia en vivienda',
            'location' => 'Comarca',
            'description' => 'Reparación exprés de una avería que dejó sin suministro a una vivienda unifamiliar.',
            'image' => 'https://loremflickr.com/800/600/plumbing,repair,pipe?lock=23',
        ],
    ],

];
