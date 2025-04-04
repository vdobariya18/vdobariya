<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



use App\Http\Controllers\UserController;

// Login routes



// Login routes
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.submit');

//
Route::middleware('CheckAuth')->group(function () {
    Route::get('/ajaxdashboard', [UserController::class, 'dashview'])->name('dashview');
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts/user/{userId}', [UserController::class, 'viewPosts'])->name('posts.view');
    Route::post('/posts/delete/{postId}', [UserController::class, 'deletePost'])->name('posts.delete');
    Route::post('/users/delete/{userId}', [UserController::class, 'deleteUser'])->name('users.delete');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/post/add', [UserController::class, 'showAddPostForm'])->name('posts.add');
Route::post('/post/add', [UserController::class, 'addPost'])->name('posts.add');
Route::get('/profile/user/{userID}', [UserController::class, 'profile'])->name('profile');
Route::put('/profile/user/{userID}', [UserController::class, 'updateProfile'])->name('profile.update');
});