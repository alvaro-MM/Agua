<?php

namespace App\Http\Controllers;

use App\Support\PublicContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'services' => PublicContent::featuredServices(),
            'projects' => PublicContent::featuredProjects(),
        ]);
    }

    public function services(): View
    {
        return view('public.services', [
            'services' => PublicContent::services(),
        ]);
    }

    public function catalog(): View
    {
        return view('public.catalog', [
            // El catálogo se muestra agrupado por categoría.
            'catalog' => PublicContent::products()->groupBy('category'),
        ]);
    }

    public function projects(): View
    {
        return view('public.projects', [
            'projects' => PublicContent::projects(),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function legalNotice(): View
    {
        return view('public.legal.notice');
    }

    public function legalPrivacy(): View
    {
        return view('public.legal.privacy');
    }

    public function sitemap(): Response
    {
        $routes = ['home', 'services', 'catalog', 'projects', 'about', 'contact', 'legal.notice', 'legal.privacy'];

        $urls = array_map(fn ($name) => route($name), $routes);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
