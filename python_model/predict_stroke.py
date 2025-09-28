import sys
import pandas as pd
import joblib

def predict(input_data):
    try:
        model = joblib.load('python_model/stroke_model.joblib')
        scaler = joblib.load('python_model/stroke_scaler.joblib')
        model_columns = joblib.load('python_model/model_columns.joblib')

        df = pd.DataFrame([input_data])
        df_processed = pd.get_dummies(df, columns=['gender', 'ever_married', 'work_type', 'Residence_type', 'smoking_status'], drop_first=True)

        for col in model_columns:
            if col not in df_processed.columns:
                df_processed[col] = 0

        df_processed = df_processed[model_columns]
        scaled_features = scaler.transform(df_processed)

        prediction = model.predict(scaled_features)
        probability = model.predict_proba(scaled_features)

        return int(prediction[0]), float(probability[0][1])

    except Exception as e:
        return str(e), 0.0

if __name__ == "__main__":
    if len(sys.argv) == 11:
        raw_inputs = {
            'gender': sys.argv[1],
            'age': float(sys.argv[2]),
            'hypertension': int(sys.argv[3]),
            'heart_disease': int(sys.argv[4]),
            'ever_married': sys.argv[5],
            'work_type': sys.argv[6],
            'Residence_type': sys.argv[7],
            'avg_glucose_level': float(sys.argv[8]),
            'bmi': float(sys.argv[9]),
            'smoking_status': sys.argv[10]
        }
    else:
        # Mode testing
        raw_inputs = {
            'gender': 'Male', 'age': 67.0, 'hypertension': 0, 'heart_disease': 1,
            'ever_married': 'Yes', 'work_type': 'Private', 'Residence_type': 'Urban',
            'avg_glucose_level': 228.69, 'bmi': 36.6, 'smoking_status': 'formerly smoked'
        }

    prediction_result, prediction_proba = predict(raw_inputs)
    print(f'{{"prediction": {prediction_result}, "probability": {prediction_proba * 100:.2f}}}')