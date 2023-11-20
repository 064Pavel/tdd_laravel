<?php

use App\Http\Controllers\Item\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/items", [ItemController::class, "index"]);
Route::get("/items/{item}", [ItemController::class, "show"]);
Route::post("/items", [ItemController::class, "store"]);
Route::patch("/items/{item}", [ItemController::class, "update"]);
Route::delete("/items/{item}", [ItemController::class, "delete"]);

Route::post("/items/add-to-cart", [ItemController::class, "addToCart"])->middleware("auth");