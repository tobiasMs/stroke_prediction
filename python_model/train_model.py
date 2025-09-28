import pandas as pd
from imblearn.over_sampling import SMOTE
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
import joblib # Import joblib untuk menyimpan model

# Pesan Awal
print("Memulai proses pelatihan model stroke...")

# 1. Baca dan Pra-pemrosesan Awal
data = pd.read_csv('healthcare-dataset-stroke-data.csv')
data = data.drop('id', axis=1)
data.dropna(axis=0, inplace=True)

# 2. One-Hot Encoding
# drop_first=True penting untuk direplikasi saat prediksi
data = pd.get_dummies(data, columns=['gender', 'ever_married', 'work_type', 'Residence_type', 'smoking_status'], drop_first=True)

# 3. Pemilihan Fitur (Sesuai kode Anda)
# Ini adalah daftar kolom yang akan digunakan model
selected_columns = [
    'age', 'hypertension', 'avg_glucose_level', 'heart_disease',
    'ever_married_Yes', 'smoking_status_formerly smoked',
    'work_type_Self-employed', 'bmi', 'smoking_status_smokes',
    'work_type_Private', 'smoking_status_never smoked', 'gender_Male',
    'Residence_type_Urban', 'stroke'
]
data_selected = data[selected_columns]

# 4. Penanganan Imbalance Data dengan SMOTE
y = data_selected['stroke']
X = data_selected.drop(columns=['stroke'])
sm = SMOTE(random_state=42)
X_resampled, y_resampled = sm.fit_resample(X, y)

# 5. Normalisasi Data
scaler = StandardScaler()
# Latih scaler HANYA pada data fitur (X), bukan pada target (y)
X_scaled = scaler.fit_transform(X_resampled)

# 6. Pelatihan Model
rforest_model = RandomForestClassifier(class_weight='balanced', random_state=42)
rforest_model.fit(X_scaled, y_resampled)

print("Pelatihan model selesai.")

# 7. SIMPAN MODEL DAN SCALER
# Ini adalah bagian terpenting untuk integrasi
joblib.dump(rforest_model, 'stroke_model.joblib')
joblib.dump(scaler, 'stroke_scaler.joblib')
joblib.dump(X.columns.tolist(), 'model_columns.joblib') # Simpan juga daftar kolom

print("Model, scaler, dan daftar kolom telah disimpan!")