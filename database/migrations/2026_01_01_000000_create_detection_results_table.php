<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_results', function (Blueprint $table) {
            $table->id();
            $table->string('original_image')->nullable()->comment('Path gambar asli di storage/public');
            $table->string('result_image')->nullable()->comment('Path gambar hasil deteksi (dengan bounding box)');
            $table->enum('model_used', ['yolov8', 'yolov11'])->default('yolov8');
            $table->longText('detections')->nullable()->comment('JSON array hasil deteksi: label, confidence, bbox');
            $table->unsignedInteger('total_defects')->default(0);
            $table->enum('status', ['good', 'defective', 'unknown'])->default('unknown');
            $table->float('processing_time')->nullable()->comment('Waktu proses dalam detik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_results');
    }
};