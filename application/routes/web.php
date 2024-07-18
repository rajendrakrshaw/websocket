<?php

use Illuminate\Support\Facades\Route;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use App\Http\Controllers\ChatController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::post('/chat/send', [ChatController::class, 'sendMessage']);


WebSocketsRouter::webSocket('/my-websocket', \App\WebSockets\MyWebSocketHandler::class);


// WebSocketsRouter::webSocket('/my-websocket', \App\WebSockets\MyWebSocketHandler::class);
Route::get('/chat', function () {
    return view('chat');
});

Route::get('/', function () {
    return view('welcome');
});
