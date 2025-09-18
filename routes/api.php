<?php

use TeamTeaTime\Forum\Http\Controllers\Api\{
    Bulk\CategoryController as BulkCategoryController,
    Bulk\PostController as BulkPostController,
    Bulk\ThreadController as BulkThreadController,
    CategoryController,
    PostController,
    ThreadController,
    VoteController
};

$authMiddleware = config('forum.api.router.auth_middleware', []);
$moderatorMiddleware = config('forum.api.router.moderator_middleware', []);

// Categories
Route::prefix('category')->name('category.')->group(function () use ($moderatorMiddleware) {

    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('{category}', [CategoryController::class, 'fetch'])->name('fetch');

    Route::middleware([$moderatorMiddleware])->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::patch('{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('{category}', [CategoryController::class, 'delete'])->name('delete');
        Route::post('{category}/thread', [ThreadController::class, 'store'])->name('threads.store');
    });

    // Threads by category
    Route::get('{category}/thread', [ThreadController::class, 'indexByCategory'])->name('threads.indexByCategory');
});

// Threads
Route::prefix('thread')->name('thread.')->group(function () use ($authMiddleware, $moderatorMiddleware) {

    if (config('forum.api.enable_search')) {
        Route::post('search', [ThreadController::class, 'search'])->name('search');
    }

    Route::get('recent', [ThreadController::class, 'recent'])->name('recent');
    Route::get('unread', [ThreadController::class, 'unread'])->name('unread');
    Route::patch('unread/mark-as-read', [ThreadController::class, 'markAsRead'])->name('unread.mark-as-read')->middleware($authMiddleware);

    Route::get('{thread}', [ThreadController::class, 'fetch'])->name('fetch');

    Route::middleware([$moderatorMiddleware])->group(function () {
        Route::post('{thread}/lock', [ThreadController::class, 'lock'])->name('lock');
        Route::post('{thread}/unlock', [ThreadController::class, 'unlock'])->name('unlock');
        Route::post('{thread}/pin', [ThreadController::class, 'pin'])->name('pin');
        Route::post('{thread}/unpin', [ThreadController::class, 'unpin'])->name('unpin');
        Route::post('{thread}/rename', [ThreadController::class, 'rename'])->name('rename');
        Route::post('{thread}/move', [ThreadController::class, 'move'])->name('move');
        Route::delete('{thread}', [ThreadController::class, 'delete'])->name('delete');
        Route::post('{thread}/restore', [ThreadController::class, 'restore'])->name('restore');
    });
    Route::patch('{thread}/mark-as-read', [ThreadController::class, 'readThread'])->name('mark-as-read')->middleware($authMiddleware);

    // Posts by thread
    Route::get('{thread}/posts', [PostController::class, 'indexByThread'])->name('posts');
    Route::post('{thread}/posts', [PostController::class, 'store'])->name('posts.store')->middleware($authMiddleware);
});

// Posts
Route::prefix('post')->name('post.')->group(function () use ($authMiddleware) {
    if (config('forum.api.enable_search')) {
        Route::post('search', [PostController::class, 'search'])->name('search');
    }

    Route::get('recent', [PostController::class, 'recent'])->name('recent');
    Route::get('unread', [PostController::class, 'unread'])->name('unread');
    Route::get('{post}', [PostController::class, 'fetch'])->name('fetch');

    Route::middleware($authMiddleware)->group(function () {
        Route::patch('{post}', [PostController::class, 'update'])->name('update');
        Route::delete('{post}', [PostController::class, 'delete'])->name('delete');
        Route::post('{post}/restore', [PostController::class, 'restore'])->name('restore');
    });
});

// Bulk actions
Route::prefix('bulk')->name('bulk.')->middleware($authMiddleware)->group(function () use ($authMiddleware) {
    // Categories
    Route::prefix('category')->name('category.')->group(function () {
        Route::post('manage', [BulkCategoryController::class, 'manage'])->name('manage');
    });

    // Threads
    Route::prefix('thread')->name('thread.')->group(function () {
        Route::post('move', [BulkThreadController::class, 'move'])->name('move');
        Route::post('lock', [BulkThreadController::class, 'lock'])->name('lock');
        Route::post('unlock', [BulkThreadController::class, 'unlock'])->name('unlock');
        Route::post('pin', [BulkThreadController::class, 'pin'])->name('pin');
        Route::post('unpin', [BulkThreadController::class, 'unpin'])->name('unpin');
        Route::delete('/', [BulkThreadController::class, 'delete'])->name('delete');
        Route::post('restore', [BulkThreadController::class, 'restore'])->name('restore');
    });

    // Posts
    Route::prefix('post')->name('post.')->group(function () {
        Route::delete('/', [BulkPostController::class, 'delete'])->name('delete');
        Route::post('restore', [BulkPostController::class, 'restore'])->name('restore');
    });
});

// Votes
Route::prefix('votes')->group(function () use ($authMiddleware) {
    Route::post('/', [VoteController::class, 'store'])->name('vote.store')->middleware($authMiddleware);
});
