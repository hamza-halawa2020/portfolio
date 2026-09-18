<?php

use App\Http\Controllers\Api\V1\AboutController;
use App\Http\Controllers\Api\V1\BlogCategoryController;
use App\Http\Controllers\Api\V1\BlogPostController;
use App\Http\Controllers\Api\V1\ProjectCategoryController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SiteController;
use App\Http\Controllers\Api\V1\SocialLinkController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\TechnologyController;
use App\Http\Controllers\Api\V1\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('site', SiteController::class)->name('site');
    Route::get('about', AboutController::class)->name('about');
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('project-categories', ProjectCategoryController::class)->name('project-categories.index');
    Route::get('technologies', TechnologyController::class)->name('technologies.index');
    Route::get('testimonials', TestimonialController::class)->name('testimonials.index');
    Route::get('posts', [BlogPostController::class, 'index'])->name('posts.index');
    Route::get('posts/{slug}', [BlogPostController::class, 'show'])->name('posts.show');
    Route::get('blog-categories', BlogCategoryController::class)->name('blog-categories.index');
    Route::get('tags', TagController::class)->name('tags.index');
    Route::get('services', ServiceController::class)->name('services.index');
    Route::get('social-links', SocialLinkController::class)->name('social-links.index');
});
