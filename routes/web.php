<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
 });
 
 
 Route::get('/login', function () {
     return redirect(route('filament.admin.auth.login'));
 })->name('login');
