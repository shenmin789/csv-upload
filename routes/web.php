<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\FileUploader;
use App\Http\Resources\ProductResource;
use App\Http\Resources\FileUploadResource;
use App\Models\Product;
use App\Models\FileUpload;

// Route::get('/', function () {
//     return view('dashboard');
// })->name('home');

// Route::get('/', function () {
//     return view('test');
// });
Route::get('/', FileUploader::class)->name('home');
Route::get('/products', function () {
    return ProductResource::collection(Product::all());
});
Route::get('/file-uploads', function () {
    return FileUploadResource::collection(FileUpload::all());
});

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// // Route::view('dashboard', 'dashboard')->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

//     Route::get('settings/profile', Profile::class)->name('settings.profile');
//     Route::get('settings/password', Password::class)->name('settings.password');
//     Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
// });

require __DIR__.'/auth.php';
