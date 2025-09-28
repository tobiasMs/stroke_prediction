import pandas as pd
import joblib
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn

# 1. Inisialisasi Aplikasi FastAPI
app = FastAPI(
    title="Stroke Prediction API",
    description="API untuk memprediksi risiko stroke berdasarkan data pasien.",
    version="1.0.0"
)

# 2. Muat Model dan Artefak Lainnya
try:
    model = joblib.load('stroke_model.joblib')
    scaler = joblib.load('stroke_scaler.joblib')
    model_columns = joblib.load('model_columns.joblib')
    print("Model, scaler, dan kolom berhasil dimuat.")
except FileNotFoundError as e:
    print(f"Error: Salah satu file .joblib tidak ditemukan. Pastikan file berada di direktori yang sama.")
    model = None # Tandai bahwa model gagal dimuat
except Exception as e:
    print(f"Error saat memuat model: {e}")
    model = None

# 3. Definisikan Struktur Input Data menggunakan Pydantic
# Ini akan menjadi format JSON yang diterima API dan divalidasi secara otomatis.
# Nama field harus sesuai dengan kolom *sebelum* di one-hot encode.
class PatientData(BaseModel):
    age: float
    hypertension: int
    heart_disease: int
    avg_glucose_level: float
    bmi: float
    gender: str          # Contoh: "Male", "Female"
    ever_married: str    # Contoh: "Yes", "No"
    work_type: str       # Contoh: "Private", "Self-employed", dll.
    Residence_type: str  # Contoh: "Urban", "Rural"
    smoking_status: str  # Contoh: "smokes", "formerly smoked", dll.

    class Config:
        schema_extra = {
            "example": {
                "age": 67.0,
                "hypertension": 0,
                "heart_disease": 1,
                "avg_glucose_level": 228.69,
                "bmi": 36.6,
                "gender": "Female",
                "ever_married": "Yes",
                "work_type": "Private",
                "Residence_type": "Urban",
                "smoking_status": "formerly smoked"
            }
        }

# 4. Definisikan Endpoint Prediksi
@app.post("/predict")
def predict_stroke(data: PatientData):
    """
    Endpoint untuk menerima data pasien dan mengembalikan prediksi risiko stroke.
    """
    if not model or not scaler or not model_columns:
        raise HTTPException(status_code=503, detail="Model tidak tersedia. Server belum siap.")

    try:
        # --- PERUBAHAN UTAMA: PREPROCESSING YANG BENAR ---
        # 1. Buat dictionary fitur dengan semua kolom yang dibutuhkan model, inisialisasi dengan 0
        feature_dict = {col: 0 for col in model_columns}

        # 2. Isi nilai numerik dasar dari input
        feature_dict['age'] = data.age
        feature_dict['hypertension'] = data.hypertension
        feature_dict['heart_disease'] = data.heart_disease
        feature_dict['avg_glucose_level'] = data.avg_glucose_level
        feature_dict['bmi'] = data.bmi

        # 3. Bangun nama kolom one-hot-encoded secara manual dan set nilainya ke 1
        # Ini mereplikasi proses `get_dummies(..., drop_first=True)`
        if data.gender != 'Female': # Asumsi 'Female' adalah kategori yang di-drop
            feature_dict[f'gender_{data.gender}'] = 1
        if data.ever_married == 'Yes':
            feature_dict['ever_married_Yes'] = 1
        if data.work_type != 'Govt_job': # Asumsi 'Govt_job' atau kategori lain di-drop
            if f'work_type_{data.work_type}' in feature_dict:
                 feature_dict[f'work_type_{data.work_type}'] = 1
        if data.Residence_type == 'Urban':
            feature_dict['Residence_type_Urban'] = 1
        if data.smoking_status != 'Unknown': # Asumsi 'Unknown' atau kategori lain di-drop
             if f'smoking_status_{data.smoking_status.replace(" ", "_")}' in feature_dict:
                 feature_dict[f'smoking_status_{data.smoking_status.replace(" ", "_")}'] = 1


        # 4. Ubah dictionary menjadi DataFrame dengan urutan kolom yang benar
        df = pd.DataFrame([feature_dict])
        df = df[model_columns] # Pastikan urutan kolom 100% sama

        # 5. Lakukan scaling dan prediksi
        scaled_features = scaler.transform(df)
        prediction = model.predict(scaled_features)
        probability = model.predict_proba(scaled_features)

        return {
            "prediction": int(prediction[0]),
            "prediction_label": "Stroke" if int(prediction[0]) == 1 else "No Stroke",
            "probability_percent": f"{float(probability[0][1]) * 100:.2f}%"
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan saat pemrosesan: {str(e)}")

# Endpoint dasar untuk mengecek status API
@app.get("/")
def read_root():
    return {"status": "Stroke Prediction API is running."}

# Perintah untuk menjalankan server (jika file ini dieksekusi langsung)
if __name__ == '__main__':
    uvicorn.run(app, host="127.0.0.1", port=5000)
