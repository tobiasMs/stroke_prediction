<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Penyakit Stroke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Form Prediksi Stroke</h1>

        @if(isset($result))
            <div class="alert {{ $result['prediction'] == 1 ? 'alert-danger' : 'alert-success' }} mt-4">
                <h4 class="alert-heading">Hasil Prediksi:</h4>
                @if($result['prediction'] == 1)
                    <p><strong>Risiko Stroke: TINGGI</strong></p>
                    <p>Berdasarkan data yang dimasukkan, model memprediksi adanya risiko stroke sebesar <strong>{{ $result['probability'] }}%</strong>.</p>
                    <hr>
                    <p class="mb-0">Segera konsultasikan dengan dokter untuk pemeriksaan lebih lanjut. Hasil ini bukan diagnosis medis.</p>
                @else
                    <p><strong>Risiko Stroke: RENDAH</strong></p>
                     <p>Berdasarkan data yang dimasukkan, model memprediksi risiko stroke hanya sebesar <strong>{{ $result['probability'] }}%</strong>.</p>
                     <hr>
                    <p class="mb-0">Tetap jaga gaya hidup sehat. Hasil ini bukan diagnosis medis.</p>
                @endif
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                Masukkan Data Pasien
            </div>
            <div class="card-body">
                <form action="{{ url('/predict') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="Male" {{ (isset($inputs) && $inputs['gender'] == 'Male') ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Female" {{ (isset($inputs) && $inputs['gender'] == 'Female') ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="age" class="form-label">Usia</label>
                                <input type="number" step="1" class="form-control" id="age" name="age" value="{{ $inputs['age'] ?? '50' }}" required>
                            </div>
                             <div class="mb-3">
                                <label for="avg_glucose_level" class="form-label">Rata-rata Kadar Glukosa</label>
                                <input type="number" step="0.01" class="form-control" id="avg_glucose_level" name="avg_glucose_level" value="{{ $inputs['avg_glucose_level'] ?? '100' }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="bmi" class="form-label">Indeks Massa Tubuh (BMI)</label>
                                <input type="number" step="0.1" class="form-control" id="bmi" name="bmi" value="{{ $inputs['bmi'] ?? '28.1' }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="smoking_status" class="form-label">Status Merokok</label>
                                <select class="form-select" id="smoking_status" name="smoking_status" required>
                                    <option value="formerly smoked" {{ (isset($inputs) && $inputs['smoking_status'] == 'formerly smoked') ? 'selected' : '' }}>Dulu Merokok</option>
                                    <option value="never smoked" {{ (isset($inputs) && $inputs['smoking_status'] == 'never smoked') ? 'selected' : '' }}>Tidak Pernah Merokok</option>
                                    <option value="smokes" {{ (isset($inputs) && $inputs['smoking_status'] == 'smokes') ? 'selected' : '' }}>Merokok</option>
                                    <option value="Unknown" {{ (isset($inputs) && $inputs['smoking_status'] == 'Unknown') ? 'selected' : '' }}>Tidak Diketahui</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hypertension" class="form-label">Riwayat Hipertensi</label>
                                <select class="form-select" id="hypertension" name="hypertension" required>
                                    <option value="0" {{ (isset($inputs) && $inputs['hypertension'] == '0') ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ (isset($inputs) && $inputs['hypertension'] == '1') ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="heart_disease" class="form-label">Riwayat Penyakit Jantung</label>
                                <select class="form-select" id="heart_disease" name="heart_disease" required>
                                    <option value="0" {{ (isset($inputs) && $inputs['heart_disease'] == '0') ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ (isset($inputs) && $inputs['heart_disease'] == '1') ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="ever_married" class="form-label">Status Pernikahan</label>
                                <select class="form-select" id="ever_married" name="ever_married" required>
                                    <option value="Yes" {{ (isset($inputs) && $inputs['ever_married'] == 'Yes') ? 'selected' : '' }}>Sudah Menikah</option>
                                    <option value="No" {{ (isset($inputs) && $inputs['ever_married'] == 'No') ? 'selected' : '' }}>Belum Menikah</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="work_type" class="form-label">Jenis Pekerjaan</label>
                                <select class="form-select" id="work_type" name="work_type" required>
                                    <option value="Private" {{ (isset($inputs) && $inputs['work_type'] == 'Private') ? 'selected' : '' }}>Swasta</option>
                                    <option value="Self-employed" {{ (isset($inputs) && $inputs['work_type'] == 'Self-employed') ? 'selected' : '' }}>Wiraswasta</option>
                                    <option value="Govt_job" {{ (isset($inputs) && $inputs['work_type'] == 'Govt_job') ? 'selected' : '' }}>PNS</option>
                                    <option value="children" {{ (isset($inputs) && $inputs['work_type'] == 'children') ? 'selected' : '' }}>Anak-anak</option>
                                    <option value="Never_worked" {{ (isset($inputs) && $inputs['work_type'] == 'Never_worked') ? 'selected' : '' }}>Tidak Pernah Bekerja</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="residence_type" class="form-label">Tipe Tempat Tinggal</label>
                                <select class="form-select" id="residence_type" name="residence_type" required>
                                    <option value="Urban" {{ (isset($inputs) && $inputs['residence_type'] == 'Urban') ? 'selected' : '' }}>Perkotaan</option>
                                    <option value="Rural" {{ (isset($inputs) && $inputs['residence_type'] == 'Rural') ? 'selected' : '' }}>Pedesaan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Prediksi</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>