import pandas as pd
import joblib
from flask import Flask, request, jsonify
import traceback # <-- Tambahkan import ini

# Inisialisasi aplikasi Flask
app = Flask(__name__)

# Muat model, scaler, dan kolom
try:
    model = joblib.load('stroke_model.joblib')
    scaler = joblib.load('stroke_scaler.joblib')
    model_columns = joblib.load('model_columns.joblib')
    print("Model berhasil dimuat.")
except Exception as e:
    print(f"Error saat memuat model: {e}")
    model = None

def preprocess_and_predict(input_data):
    # Fungsi ini tetap sama
    if model is None:
        raise RuntimeError("Model tidak berhasil dimuat, server tidak bisa melakukan prediksi.")
        
    df = pd.DataFrame([input_data])
    df_processed = pd.get_dummies(df, columns=['gender', 'ever_married', 'work_type', 'residence_type', 'smoking_status'], drop_first=True)

    for col in model_columns:
        if col not in df_processed.columns:
            df_processed[col] = 0
    
    df_processed = df_processed[model_columns]
    scaled_features = scaler.transform(df_processed)

    prediction = model.predict(scaled_features)
    probability = model.predict_proba(scaled_features)
    
    return int(prediction[0]), float(probability[0][1])

# Definisikan endpoint API di /predict
@app.route('/predict', methods=['POST'])
def handle_prediction():
    # PERUBAHAN UTAMA ADA DI SINI
    try:
        if not request.json:
            return jsonify({"error": "Request harus dalam format JSON"}), 400
        
        data = request.get_json()
        prediction_result, prediction_proba = preprocess_and_predict(data)
        
        return jsonify({
            "prediction": prediction_result,
            "probability": f"{prediction_proba * 100:.2f}"
        })
        
    except Exception as e:
        # Jika terjadi error apapun, tangkap detailnya dan kirim sebagai respons
        error_details = traceback.format_exc()
        return jsonify({
            "error": "Terjadi kesalahan internal pada server Python.",
            "details": str(e),
            "traceback": error_details
            }), 500

# Jalankan server
if __name__ == '__main__':
    app.run(port=5000, debug=True)