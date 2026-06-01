<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectionController;

// Halaman utama (landing page)
Route::get('/', [DetectionController::class, 'index'])->name('home');

// Halaman deteksi
Route::get('/detect', [DetectionController::class, 'detectPage'])->name('detect');

// API endpoint deteksi (dipanggil via AJAX/fetch dari JS)
Route::post('/detect', [DetectionController::class, 'detect'])->name('detect.process');

// Riwayat deteksi
Route::get('/history', [DetectionController::class, 'history'])->name('history');

// Detail & hapus hasil
Route::get('/detection/{id}', [DetectionController::class, 'show'])->name('detection.show');
Route::delete('/detection/{id}', [DetectionController::class, 'destroy'])->name('detection.destroy');