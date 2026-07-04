<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PageController extends Controller
{
    public function home()
    {
        return view('public.home');
    }

    public function services()
    {
        return view('public.services');
    }

    public function catalog()
    {
        return view('public.catalog');
    }

    public function projects()
    {
        return view('public.projects');
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function legalNotice()
    {
        return view('public.legal.notice');
    }

    public function legalPrivacy()
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
