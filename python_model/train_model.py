import matplotlib.pyplot as plt
import seaborn as sns
import numpy as np
import pandas as pd
from sklearn.preprocessing import LabelEncoder, OneHotEncoder
from sklearn.utils import class_weight, resample
from imblearn.over_sampling import SMOTE
from sklearn import preprocessing
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split
from keras.layers import Dense, Dropout
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import precision_score, recall_score, confusion_matrix, classification_report, accuracy_score, f1_score, ConfusionMatrixDisplay
import joblib # DITAMBAHKAN: Mengimpor library joblib

#to ignore warnings
import warnings
warnings.filterwarnings('ignore')

#Read Data
print("Membaca data...")
data = pd.read_csv('healthcare-dataset-stroke-data.csv')

# --- BAGIAN PRA-PEMROSESAN ANDA (TIDAK DIUBAH) ---
data = data.drop('id', axis=1)
data.dropna(axis=0, inplace=True)
data = pd.get_dummies(data, columns=['gender', 'ever_married', 'work_type', 'Residence_type', 'smoking_status'], drop_first=True)

# --- BAGIAN ANALISIS KORELASI ANDA (TIDAK DIUBAH) ---
numerical_columns= data.select_dtypes(['int','float','bool'])
corr_matrix = numerical_columns.corr()
plt.figure(figsize=(16, 16))
sns.heatmap(corr_matrix, annot=True, linewidths=0.5)
plt.title('Numerical Columns Correlation Matrix')
plt.show()

corr_matrix = data.corr(numeric_only=True)
corr_with_stroke = corr_matrix['stroke'].drop('stroke')
print("Korelasi fitur dengan 'stroke':")
print(corr_with_stroke.sort_values(ascending=False))

# --- BAGIAN PEMILIHAN FITUR ANDA (TIDAK DIUBAH) ---
selected_columns = [
    'age',
    'hypertension',
    'avg_glucose_level',
    'heart_disease',
    'ever_married_Yes',
    'smoking_status_formerly smoked',
    'work_type_Self-employed',
    'bmi',
    'smoking_status_smokes',
    'work_type_Private',
    'smoking_status_never smoked',
    'gender_Male',
    'Residence_type_Urban',
    'stroke'
]
numerical_columns = data[selected_columns]

# --- BAGIAN PERSIAPAN & PELATIHAN MODEL ANDA (TIDAK DIUBAH) ---
print("\nMemulai persiapan dan pelatihan model...")
y = numerical_columns['stroke']
X = numerical_columns.drop(columns=['stroke'])
sm = SMOTE()
X_resampled, y_resampled = sm.fit_resample(X, y)

X_train, X_test, y_train, y_test = train_test_split(X_resampled, y_resampled, test_size=0.051, random_state=1)

scaler = StandardScaler()
X_train_scaled = scaler.fit_transform(X_train)
X_test_scaled = scaler.transform(X_test)

rforest_model = RandomForestClassifier(class_weight='balanced')
rforest_model.fit(X_train_scaled, y_train)

# --- BAGIAN EVALUASI ANDA (TIDAK DIUBAH) ---
print("\n--- HASIL EVALUASI MODEL ---")
score = rforest_model.score(X_test_scaled, y_test)
print(f"Skor Akurasi Model: {score:.4f}")

y_pred = rforest_model.predict(X_test_scaled)
print("Classification Report:\n", classification_report(y_test, y_pred))

cm_rforest = confusion_matrix(y_test, y_pred)
plt.figure(figsize=(8, 8))
sns.heatmap(cm_rforest, annot=True, fmt='d', cmap='Blues', xticklabels=["No Stroke", "Stroke"], yticklabels=["No Stroke", "Stroke"])
plt.title('Confusion Matrix')
plt.ylabel('True label')
plt.xlabel('Predicted label')
plt.show()

# ===================================================================
# ---- DITAMBAHKAN: BAGIAN PENYIMPANAN MODEL KE JOBLIB ----
# ===================================================================
print("\nMenyimpan model, scaler, dan daftar kolom ke file joblib...")

# 1. Menyimpan model yang sudah dilatih
joblib.dump(rforest_model, 'stroke_model.joblib')

# 2. Menyimpan scaler yang sudah di-fit
joblib.dump(scaler, 'stroke_scaler.joblib')

# 3. Menyimpan daftar nama kolom fitur untuk konsistensi di API
joblib.dump(X.columns.tolist(), 'model_columns.joblib')

print("Penyimpanan selesai! File 'stroke_model.joblib', 'stroke_scaler.joblib', dan 'model_columns.joblib' sudah siap.")