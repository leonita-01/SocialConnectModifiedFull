<?php

use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FollowerController;
use App\Http\Controllers\API\FriendshipController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\LoginRegisterController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\PhotoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\ClientController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use Laravel\Passport\Http\Controllers\ScopeController;

// Passport routes
Route::group(['middleware' => ['api']], function () {
    Route::post('oauth/token', [AccessTokenController::class, 'issueToken']);
    Route::get('oauth/authorize', [AuthorizationController::class, 'authorize']);
    Route::post('oauth/authorize', [AuthorizationController::class, 'approve']);
    Route::delete('oauth/authorize', [AuthorizationController::class, 'deny']);
    Route::post('oauth/token/refresh', [TransientTokenController::class, 'refresh']);
    Route::post('oauth/token/revoke', [AccessTokenController::class, 'revoke']);

    Route::get('oauth/clients', [ClientController::class, 'forUser']);
    Route::post('oauth/clients', [ClientController::class, 'store']);
    Route::put('oauth/clients/{client_id}', [ClientController::class, 'update']);
    Route::delete('oauth/clients/{client_id}', [ClientController::class, 'destroy']);

    Route::get('oauth/personal-access-tokens', [PersonalAccessTokenController::class, 'forUser']);
    Route::post('oauth/personal-access-tokens', [PersonalAccessTokenController::class, 'store']);
    Route::delete('oauth/personal-access-tokens/{token_id}', [PersonalAccessTokenController::class, 'destroy']);

    Route::get('oauth/scopes', [ScopeController::class, 'all']);
});

Route::prefix('auth')->group(function () {
    Route::controller(LoginRegisterController::class)->group(function() {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::delete('/logout', 'logout')->middleware('auth:sanctum');
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(ProfileController::class)->prefix('user')->group(function() {
        Route::get('/profile', 'profile');
        Route::put('/profile', 'updateProfile');
        Route::get('/{id}', 'getUserProfile');
    });

    // Post routes
    Route::controller(PostController::class)->prefix('posts')->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::put('/{id}/like', 'toggleLike');
    });

    // Friendship routes
    Route::controller(FriendshipController::class)->prefix('friends')->group(function() {
        Route::get('/pending','pending');
        Route::get('/', 'index');
        Route::post('/','store');
        Route::put('/{friendship}', 'update');
        Route::delete('/{friendship}', 'destroy');
    });

    Route::controller(GroupController::class)->prefix('groups')->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{group}', 'show');
        Route::put('/{group}', 'update');
        Route::delete('/{group}', 'destroy');
    });

    // Story routes
    Route::controller(StoryController::class)->prefix('stories')->group(function() {
        Route::post('/upload', 'upload');
        Route::get('/active', 'viewActiveStories');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('comments')->group(function() {
        Route::get('/', [CommentController::class, 'index']);
        Route::post('/', [CommentController::class, 'store']);
        Route::get('/{comment}', [CommentController::class, 'show']);
        Route::put('/{comment}', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });

});

// Followers routes
Route::prefix('followers')->group(function() {
    Route::get('/', [FollowerController::class, 'index'])->name('followers.index');
    Route::post('/follow', [FollowerController::class, 'follow'])->name('followers.follow');
    Route::delete('/unfollow', [FollowerController::class, 'unfollow'])->name('followers.unfollow');
});

// Photo routes
Route::prefix('photos')->group(function () {
    Route::get('/', [PhotoController::class, 'index']);
    Route::get('/{id}', [PhotoController::class, 'show']);
    Route::post('/', [PhotoController::class, 'store']);
    Route::put('/{id}', [PhotoController::class, 'update']);
    Route::delete('/{id}', [PhotoController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->controller(CommentController::class)->prefix('comments')->group(function () {
    Route::post('/', 'store')->name('comments.store');
    Route::put('/{comment}', 'update')->name('comments.update');
    Route::get('/{comment}', 'show')->name('comments.show');
    Route::delete('/{comment}', 'destroy')->name('comments.destroy');
});

// Additional routes for UserController
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
