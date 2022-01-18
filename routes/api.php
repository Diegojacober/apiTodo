<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function () {
    return [
        'pong' => true
    ];
});


Route::get('/unauthenticated', function () {
    return ['error' => 'Usuário não autenticado'];
})->name('login');
//users
Route::post('/user', [AuthController::class, 'create']);
Route::middleware('auth:sanctum')->get('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth', [AuthController::class, 'login']);

//CREATE
Route::middleware('auth:sanctum')->post('/todo', [TodoController::class, 'create']);

//READ
Route::get('/todos', [TodoController::class, 'readAll']);
Route::get('/todo/{id}', [TodoController::class, 'readTodo']);

//UPDATE
Route::middleware('auth:sanctum')->put('/todo/{id}', [TodoController::class, 'update']);

//DELETE
Route::middleware('auth:sanctum')->delete('/todo/{id}', [TodoController::class, 'delete']);

//enviando arquivos para uma api

Route::post('/upload', function (Request $request) {
    $array = ['error' => ''];

    $rules = [
        'name' => 'required|min:4',
        'foto' => 'mimes:jpg,jpeg,png'
    ];

    $validator = Validator::make($request->all(),$rules);

    if($validator->fails()){
        $array['error'] = $validator->errors();
        return $array;
    }

    if ($request->hasFile('foto')) {
        if ($request->file('foto')->isValid()) {
            //$ext = $request->file('foto')->extension();
            //$array['ext'] = $ext;
            $foto = $request->file('foto')->store('public');
            $url = asset(Storage::url($foto));
            $array['url'] = $url;

        }
    } else {
        $array['error'] = 'Não foi enviado nenhum arquivo';
    }

    return $array;
});
