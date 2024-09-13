<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
 });
 
 Route::get('/login', function () {
     return redirect(route('filament.admin.auth.login'));
 })->name('login');

//  Route::middleware(['auth', 'check.license'])->group(function () {
//     Route::get('/admin', function () {
//         return redirect(route('filament.admin.auth.login'));
//     })->name('admin.dashboard');
// });