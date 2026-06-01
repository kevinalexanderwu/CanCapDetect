<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YoloService
{
    protected string $apiUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('yolo.api_url', env('YOLO_API_URL', 'http://127.0.0.1:8001'));
        $this->timeout = (int) config('yolo.timeout', 60);
    }

    /**
     * Kirim gambar ke Python API dan kembalikan hasil deteksi
     *
     * @param string $imagePath  Full path ke file gambar
     * @param string $model      'yolov8' atau 'yolov11'
     * @return array
     */
    public function detect(string $imagePath, string $model): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->attach('image', file_get_contents($imagePath), basename($imagePath))
                ->post("{$this->apiUrl}/detect", [
                    'model' => $model,
                ]);

            if ($response->failed()) {
                Log::error('YOLO API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [
                    'success' => false,
                    'message' => 'Python API returned error: ' . $response->status(),
                ];
            }

            $data = $response->json();

            // Simpan result image dari base64 (jika API mengembalikan base64)
            $resultImageUrl = null;
            if (!empty($data['result_image_base64'])) {
                $resultImagePath = $this->saveBase64Image(
                    $data['result_image_base64'],
                    'results/' . uniqid('result_') . '.jpg'
                );
                $resultImageUrl = url('storage/' . $resultImagePath);
                $data['result_image']     = $resultImagePath;
                $data['result_image_url'] = $resultImageUrl;
            }

            return array_merge(['success' => true], $data);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('YOLO API connection failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Cannot connect to detection service. Make sure Python API is running.',
            ];
        } catch (\Exception $e) {
            Log::error('YOLO Service exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Internal error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Simpan base64 image ke storage
     */
    protected function saveBase64Image(string $base64, string $path): string
    {
        $imageData = base64_decode($base64);
        Storage::disk('public')->put($path, $imageData);
        return $path;
    }

    /**
     * Cek apakah Python API aktif
     */
    public function ping(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Ambil daftar model yang tersedia dari Python API
     */
    public function getAvailableModels(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/models");
            if ($response->successful()) {
                return $response->json('models', []);
            }
        } catch (\Exception $e) {
            Log::warning('Could not fetch models from API', ['error' => $e->getMessage()]);
        }

        // Fallback default
        return ['yolov8', 'yolov11'];
    }
}