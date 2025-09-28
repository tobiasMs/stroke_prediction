<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class StrokePredictionController extends Controller
{
    public function showForm()
    {
        return view('predict');
    }

    public function predict(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'required|string',
            'age' => 'required|numeric|min:0',
            'hypertension' => 'required|integer|in:0,1',
            'heart_disease' => 'required|integer|in:0,1',
            'ever_married' => 'required|string',
            'work_type' => 'required|string',
            'residence_type' => 'required|string',
            'avg_glucose_level' => 'required|numeric|min:0',
            'bmi' => 'required|numeric|min:0',
            'smoking_status' => 'required|string',
        ]);
        
        // ===================================================================
        // PENTING: Konversi tipe data string dari form menjadi tipe data yang benar
        // ===================================================================
        $validated['age'] = (float)$validated['age'];
        $validated['hypertension'] = (int)$validated['hypertension'];
        $validated['heart_disease'] = (int)$validated['heart_disease'];
        $validated['avg_glucose_level'] = (float)$validated['avg_glucose_level'];
        $validated['bmi'] = (float)$validated['bmi'];
        // ===================================================================
        
        // try {
            // Kirim POST request ke API Flask dengan data yang sudah benar
            $response = Http::asJson()->post('http://127.0.0.1:5000/predict', $validated);
            Log::info($response);

            if ($response->successful()) {
                $result = $response->json();
                return view('predict', ['result' => $result, 'inputs' => $request->all()]);
            } else {
                $errorBody = $response->json();
                $errorMessage = $errorBody['details'] ?? ($errorBody['error'] ?? 'Terjadi error tidak dikenal dari API.');
                return back()->with('error', 'Gagal mendapatkan prediksi dari API: ' . $errorMessage)->withInput();
            }

        // } catch (ConnectionException $e) {
        //     return back()->with('error', 'Tidak dapat terhubung ke server prediksi. Pastikan server API Python sudah berjalan.')->withInput();
        // }
    }
}