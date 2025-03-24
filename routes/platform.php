<?php

declare(strict_types=1);

use App\Orchid\Layouts\VentesTabNav;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

use App\Orchid\Screens\ProductScreen;
use App\Orchid\Screens\ProductAddScreen;
use App\Orchid\Screens\ProductEditScreen;
use App\Orchid\Screens\ClientsScreen;
use App\Orchid\Screens\ClientAddScreen;
use App\Orchid\Screens\ClientEditScreen;
use App\Orchid\Screens\VentesScreen;
use App\Orchid\Screens\FactureScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/
// Platform Accueil
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform Produit
Route::screen('/product', ProductScreen::class)->name('platform.product');

// Ajouter produit 
Route::screen('/product/add', ProductAddScreen::class)->name('platform.product.add');

// Edit  produit 
Route::screen('/product/edit/{id}', ProductEditScreen::class)->name('platform.product.edit');

// Platform Clients
Route::screen('/clients', ClientsScreen::class)->name('platform.clients');

// Ajouter Clients
Route::screen('/clients/add', ClientAddScreen::class)->name('platform.clients.add');

// supprimer un Client  
Route::delete('/clients/{id}', [ClientsScreen::class, 'delete'])->name('platform.clients.delete');


// Editer un client 
Route::screen('/clients/edit/{id}', ClientEditScreen::class)->name('platform.clients.edit');


// Platform Ventes 
Route::screen('/ventes', VentesScreen::class)->name('platform.ventes');

//Platform Ventes & TabNav
Route::screen('/ventes', VentesScreen::class)->name('ventes.index');


// Plateform Facture 
Route::screen('/facture', FactureScreen::class)->name('platform.facture');











// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));


