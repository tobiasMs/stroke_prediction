<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Penyakit Stroke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 mb-5">
        <h1 class="mb-4 text-center">Form Prediksi Stroke</h1>

        {{-- Bagian untuk menampilkan hasil prediksi --}}
        @if(isset($result))
            @if(isset($result['detail']))
                 <div class="alert alert-warning mt-4">
                    <h4 class="alert-heading">Terjadi Kesalahan</h4>
                    <p>{{ $result['detail'] }}</p>
                </div>
            @else
                <div class="alert {{ $result['prediction'] == 1 ? 'alert-danger' : 'alert-success' }} mt-4">
                    <h4 class="alert-heading">Hasil Prediksi: {{ $result['prediction_label'] }}</h4>
                    @if($result['prediction'] == 1)
                        <p>Berdasarkan data yang dimasukkan, model memprediksi adanya risiko stroke sebesar <strong>{{ $result['probability_percent'] }}</strong>.</p>
                        <hr>
                        <p class="mb-0">Segera konsultasikan dengan dokter untuk pemeriksaan lebih lanjut. Hasil ini bukan diagnosis medis.</p>
                    @else
                         <p>Berdasarkan data yang dimasukkan, model memprediksi risiko stroke hanya sebesar <strong>{{ $result['probability_percent'] }}</strong>.</p>
                         <hr>
                        <p class="mb-0">Tetap jaga gaya hidup sehat. Hasil ini bukan diagnosis medis.</p>
                    @endif
                </div>
            @endif
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
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin...</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="age" class="form-label">Usia</label>
                                {{-- DIUBAH: Menghapus nilai default '50' --}}
                                <input type="number" step="1" class="form-control" id="age" name="age" value="{{ old('age') }}" placeholder="Contoh: 55" required>
                            </div>
                             <div class="mb-3">
                                <label for="avg_glucose_level" class="form-label">Rata-rata Kadar Glukosa</label>
                                {{-- DIUBAH: Menghapus nilai default '100' --}}
                                <input type="number" step="0.01" class="form-control" id="avg_glucose_level" name="avg_glucose_level" value="{{ old('avg_glucose_level') }}" placeholder="Contoh: 120.55" required>
                            </div>
                            <div class="mb-3">
                                <label for="bmi" class="form-label">Indeks Massa Tubuh (BMI)</label>
                                {{-- DIUBAH: Menghapus nilai default '28.1' --}}
                                <input type="number" step="0.1" class="form-control" id="bmi" name="bmi" value="{{ old('bmi') }}" placeholder="Contoh: 29.7" required>
                            </div>
                            <div class="mb-3">
                                <label for="smoking_status" class="form-label">Status Merokok</label>
                                <select class="form-select" id="smoking_status" name="smoking_status" required>
                                    <option value="" disabled {{ old('smoking_status') ? '' : 'selected' }}>Pilih Status Merokok...</option>
                                    <option value="formerly smoked" {{ old('smoking_status') == 'formerly smoked' ? 'selected' : '' }}>Dulu Merokok</option>
                                    <option value="never smoked" {{ old('smoking_status') == 'never smoked' ? 'selected' : '' }}>Tidak Pernah Merokok</option>
                                    <option value="smokes" {{ old('smoking_status') == 'smokes' ? 'selected' : '' }}>Merokok</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hypertension" class="form-label">Riwayat Hipertensi</label>
                                <select class="form-select" id="hypertension" name="hypertension" required>
                                     <option value="" disabled {{ old('hypertension') ? '' : 'selected' }}>Pilih Riwayat...</option>
                                    <option value="0" {{ old('hypertension') == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('hypertension') == '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="heart_disease" class="form-label">Riwayat Penyakit Jantung</label>
                                <select class="form-select" id="heart_disease" name="heart_disease" required>
                                     <option value="" disabled {{ old('heart_disease') ? '' : 'selected' }}>Pilih Riwayat...</option>
                                    <option value="0" {{ old('heart_disease') == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('heart_disease') == '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="ever_married" class="form-label">Status Pernikahan</label>
                                <select class="form-select" id="ever_married" name="ever_married" required>
                                     <option value="" disabled {{ old('ever_married') ? '' : 'selected' }}>Pilih Status...</option>
                                    <option value="Yes" {{ old('ever_married') == 'Yes' ? 'selected' : '' }}>Sudah Menikah</option>
                                    <option value="No" {{ old('ever_married') == 'No' ? 'selected' : '' }}>Belum Menikah</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="work_type" class="form-label">Jenis Pekerjaan</label>
                                <select class="form-select" id="work_type" name="work_type" required>
                                     <option value="" disabled {{ old('work_type') ? '' : 'selected' }}>Pilih Jenis Pekerjaan...</option>
                                    <option value="Private" {{ old('work_type') == 'Private' ? 'selected' : '' }}>Swasta</option>
                                    <option value="Self-employed" {{ old('work_type') == 'Self-employed' ? 'selected' : '' }}>Wiraswasta</option>
                                    <option value="Govt_job" {{ old('work_type') == 'Govt_job' ? 'selected' : '' }}>PNS</option>
                                    <option value="children" {{ old('work_type') == 'children' ? 'selected' : '' }}>Pelajar</option>
                                    <option value="Never_worked" {{ old('work_type') == 'Never_worked' ? 'selected' : '' }}>Tidak Pernah Bekerja</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="Residence_type" class="form-label">Tipe Tempat Tinggal</label>
                                <select class="form-select" id="Residence_type" name="Residence_type" required>
                                     <option value="" disabled {{ old('Residence_type') ? '' : 'selected' }}>Pilih Tipe...</option>
                                    <option value="Urban" {{ old('Residence_type') == 'Urban' ? 'selected' : '' }}>Perkotaan</option>
                                    <option value="Rural" {{ old('Residence_type') == 'Rural' ? 'selected' : '' }}>Pedesaan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Prediksi</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

