<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/servicios', [PageController::class, 'services'])->name('services');
Route::get('/catalogo', [PageController::class, 'catalog'])->name('catalog');
Route::get('/proyectos', [PageController::class, 'projects'])->name('projects');
Route::get('/sobre-nosotros', [PageController::class, 'about'])->name('about');

Route::get('/contacto', [PageController::class, 'contact'])->name('contact');
Route::post('/contacto', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/aviso-legal', [PageController::class, 'legalNotice'])->name('legal.notice');
Route::get('/privacidad', [PageController::class, 'legalPrivacy'])->name('legal.privacy');

Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
