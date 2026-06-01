<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YoloService;
use App\Models\DetectionResult;
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    protected $yoloService;

    public function __construct(YoloService $yoloService)
    {
        $this->yoloService = $yoloService;
    }

    /**
     * Tampilkan halaman utama
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Tampilkan halaman deteksi (detect.blade.php)
     */
    public function detectPage()
    {
        return view('detect');
    }

    /**
     * Proses deteksi gambar yang diupload
     */
    public function detect(Request $request)
    {
        $request->validate([
            'image'  => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'model'  => 'required|in:yolov8,yolov11',
        ]);

        // Simpan gambar upload
        $imagePath = $request->file('image')->store('uploads', 'public');
        $fullImagePath = Storage::disk('public')->path($imagePath);

        // Panggil Python API
        $result = $this->yoloService->detect($fullImagePath, $request->model);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Detection failed',
            ], 500);
        }

        // Simpan hasil ke database
        $detection = DetectionResult::create([
            'original_image' => $imagePath,
            'result_image'   => $result['result_image'] ?? null,
            'model_used'     => $request->model,
            'detections'     => json_encode($result['detections'] ?? []),
            'total_defects'  => $result['total_defects'] ?? 0,
            'status' => ($result['total_defects'] ?? 0) > 0 ? 'defective' : 'good',
            'processing_time'=> $result['processing_time'] ?? null,
        ]);

        return response()->json([
            'success'        => true,
            'detection_id'   => $detection->id,
            'result_image'   => $result['result_image_url'] ?? null,
            'detections'     => $result['detections'] ?? [],
            'total_defects'  => $result['total_defects'] ?? 0,
            'status'         => $detection->status,
            'processing_time'=> $result['processing_time'] ?? null,
            'model_used'     => $request->model,
        ]);
    }

    /**
     * Riwayat deteksi
     */
    public function history()
    {
        $results = DetectionResult::latest()->paginate(12);
        return view('history', compact('results'));
    }

    /**
     * Detail satu hasil deteksi
     */
    public function show($id)
    {
        $detection = DetectionResult::findOrFail($id);
        return response()->json([
            'success'       => true,
            'detection'     => $detection,
            'detections'    => json_decode($detection->detections, true),
        ]);
    }

    /**
     * Hapus hasil deteksi
     */
    public function destroy($id)
    {
        $detection = DetectionResult::findOrFail($id);

        // Hapus file gambar
        if ($detection->original_image) {
            Storage::disk('public')->delete($detection->original_image);
        }
        if ($detection->result_image) {
            Storage::disk('public')->delete($detection->result_image);
        }

        $detection->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}