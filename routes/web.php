<?php

use App\Http\Livewire\Main;
use App\Models\Short;
use Illuminate\Support\Facades\Route;

Route::get('/', Main::class)->name('main');

Route::get('{code}', function($code){
    $link = Short::whereCode($code)->first();
    abort_if(!isset($link), 404);
    $link->update([
        'last_hit' => now(),
        'counter' => $link->counter + 1
    ]);
    return redirect($link->url);
})->name('link');
