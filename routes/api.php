<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\VolumeController;



//! Auth controller
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/sign-in', 'login');
});

//! RegisterController
Route::prefix('register')->controller(RegisterController::class)->group(function () {
    Route::post('/new-trees', 'addTree');
    Route::get('/get-tree-info', 'getTreeInfo');
    Route::get('/get-tree-by-info', 'getTreeByInfo');
    Route::put('/edit-tree-info', 'editTreeInfo');
    Route::delete('/delete-tree', 'deleteTreeInfo');
});

//! VariableController
Route::prefix('variables')->controller(VariableController::class)->group(function () {
    Route::put('/edit-variables', 'editVariables');
    Route::post('/add-variables', 'addVariables');
    Route::get('/get', 'getVariables');
});

//! CalculationController
Route::prefix('calculation')->controller(CalculationController::class)->group(function () {
    Route::get('/sum-carbon', 'calculationCTT');
    Route::get('/all-tree', 'calculationAllTreeInfo');
    Route::post('/credit', 'calculationCredit');
});

//! VolumeController
Route::prefix('volume')->controller(VolumeController::class)->group(function () {
    Route::get('/get-verify', 'getVerify');
    Route::get('/get-trees-name', 'getCountByTreesName');
    Route::get('/get-trees-year', 'getCountByYear');
});
