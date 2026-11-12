<?php

use App\Http\Controllers\CourierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/couriers');

Route::resource('couriers', CourierController::class);
