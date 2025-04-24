<?php

declare(strict_types=1);

use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\VenteController;
use App\Orchid\Screens\ProductScreen;
use App\Orchid\Screens\ProductAddScreen;
use App\Orchid\Screens\ProductEditScreen;
use App\Orchid\Screens\ClientsScreen;
use App\Orchid\Screens\ClientAddScreen;
use App\Orchid\Screens\ClientEditScreen;
use App\Orchid\Screens\VentesScreen;
use App\Orchid\Screens\FactureScreen;
use App\Orchid\Screens\DashbordScreen;

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
// ----------------- Platform Accueil ---------------------
Route::screen('/index', PlatformScreen::class)->name('platform.index');



// ------------------Platform Produit------------------------
Route::screen('/product', ProductScreen::class)->name('platform.product');
    // ->breadcrumbs(function (Trail $trail) {
    //     return $trail
    //         ->parent('platform.index')
    //         ->push('Produits', route('platform.product'));
    //     });
// Ajouter produit 
Route::screen('/product/add', ProductAddScreen::class)->name('platform.product.add');
// ->breadcrumbs(function (Trail $trail) {
//     return $trail
//         ->parent('platform.product')
//         ->push('Ajouter Produit', route('platform.product.add'));
// });
// Edit  produit 
Route::screen('product/edit/{product}', ProductEditScreen::class)->name('platform.product.edit')
->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.product')
        ->push('Editer Produit', route('platform.product.edit'));
});
// supprimer un Produit
Route::delete('product/{product}', [ProductEditScreen::class, 'remove'])->name('platform.product.delete')
->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.product')
        ->push('Supprimer Produit', route('platform.product.delete'));
});




// --------------- Platform Clients ----------------

Route::screen('/clients', ClientsScreen::class)->name('platform.clients');
    // ->breadcrumbs(function (Trail $trail) {
    //     return $trail
    //         ->parent('platform.index')
    //         ->push('Clients', route('platform.clients'));
    //     });
// Ajouter Clients
Route::screen('/clients/add', ClientAddScreen::class)->name('platform.clients.add');
// ->breadcrumbs(function (Trail $trail) {
//     return $trail
//         ->parent('platform.clients')
//         ->push('Ajouter Client', route('platform.clients.add'));
// });
// supprimer un Client  
Route::delete('/clients/{id}', [ClientsScreen::class, 'delete'])->name('platform.clients.delete');


// Editer un client 
Route::screen('clients/edit/{client}', ClientEditScreen::class)->name('platform.clients.edit');
// ->breadcrumbs(function (Trail $trail) {
//     return $trail
//         ->parent('platform.clients')
//         ->push('Editer Client', route('platform.clients.edit'));
// });





// -------------------------Plateform ventes-------------------------
Route::screen('/ventes', VentesScreen::class)->name('platform.ventes');
// ->breadcrumbs(function (Trail $trail) {
//     return $trail
//         ->parent('platform.index')
//         ->push('Ventes', route('platform.ventes'));
// });




// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile');
    // ->breadcrumbs(fn (Trail $trail) => $trail
    //     ->parent('platform.index')
    //     ->push(__('Profile'), route('platform.profile')));

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
    ->name('platform.systems.users');
    // ->breadcrumbs(fn (Trail $trail) => $trail
    //     ->parent('platform.index')
    //     ->push(__('Users'), route('platform.systems.users')));


