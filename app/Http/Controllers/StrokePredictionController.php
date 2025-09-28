<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException; // Import untuk menangani error koneksi

class StrokePredictionController extends Controller
{
    /**
     * Metode untuk menampilkan halaman form.
     * Sesuai dengan: Route::get('/', [StrokePredictionController::class, 'showForm']);
     */
    public function showForm()
    {
        return view('prediction_form');
    }

    /**
     * Metode untuk memproses data form dan memanggil API.
     * NAMA METODE DISESUAIKAN menjadi 'predict'
     * Sesuai dengan: Route::post('/predict', [StrokePredictionController::class, 'predict']);
     */
    public function predict(Request $request)
    {
        // Validasi input dari form
        $validatedData = $request->validate([
            'age' => 'required|numeric',
            'hypertension' => 'required|integer',
            'heart_disease' => 'required|integer',
            'avg_glucose_level' => 'required|numeric',
            'bmi' => 'required|numeric',
            'gender' => 'required|string',
            'ever_married' => 'required|string',
            'work_type' => 'required|string',
            'Residence_type' => 'required|string',
            'smoking_status' => 'required|string',
        ]);

        try {
            // Kirim request POST ke API Python Anda
            $response = Http::post('http://127.0.0.1:5000/predict', $validatedData);

            // Kirim kembali hasil dan input lama ke view
            return view('prediction_form', [
                'result' => $response->json(),
                'inputs' => $validatedData
            ]);

        } catch (ConnectionException $e) {
            // Tangani error jika API Python tidak berjalan atau tidak bisa dihubungi
            $errorResult = [
                'detail' => 'Tidak dapat terhubung ke server prediksi. Pastikan API Python sedang berjalan.'
            ];
            return view('prediction_form', [
                'result' => $errorResult,
                'inputs' => $validatedData
            ]);
        }
    }
}