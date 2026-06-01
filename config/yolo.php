<?php

return [
    /*
    |--------------------------------------------------------------------------
    | YOLO Detection API Configuration
    |--------------------------------------------------------------------------
    | URL ke Python Flask/FastAPI yang menjalankan inferensi YOLOv8/YOLOv11
    */

    'api_url' => env('YOLO_API_URL', 'http://127.0.0.1:8001'),

    /*
    | Timeout request ke Python API (detik)
    */
    'timeout' => env('YOLO_TIMEOUT', 60),

    /*
    | Daftar model yang didukung
    */
    'models' => [
        'yolov8'  => 'YOLOv8 – Stable & Balanced',
        'yolov11' => 'YOLOv11 – Latest & Accurate',
    ],

    /*
    | Confidence threshold default (dipakai Python API)
    */
    'confidence_threshold' => 0.40,
];