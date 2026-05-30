# File: model_training_utils.py
# Location: C:\TheTradingRobotPlugWeb\Scripts\Utilities\
# Description: This script provides utility classes and functions for model training and data preprocessing in the TheTradingRobotPlug project. Key components include:
# 
# - **LoggerHandler**: Manages logging and error handling, supporting both text widget updates (for GUI applications) and standard logging.
# - **DataLoader**: Handles data loading, saving/loading scalers and metadata, and error logging.
# - **DataPreprocessor**: Preprocesses data for different model types, including handling missing values, feature engineering, and reshaping data for time-series models.
# - **VisualizationHandler**: Offers methods for visualizing and saving confusion matrices to aid in model evaluation.
# - **Setup Functions**: Includes a utility for configuring the logger with both console and file outputs.

# Key Imports:
# - `os`, `sys`, `logging`, `pickle`, `pandas`, `numpy`: Core libraries for data manipulation and logging.
# - `tensorflow.keras.models`, `sklearn.preprocessing`, `sklearn.impute`, `sklearn.model_selection`, `sklearn.metrics`: Tools for model loading, scaling, imputation, and evaluation.
# - `seaborn`, `matplotlib.pyplot`: Visualization libraries for plotting.
# - `joblib`, `json`, `traceback`: For handling model serialization, metadata management, and error traceback.


import os
import sys
import logging
import pickle
import pandas as pd
import numpy as np
from tensorflow.keras.models import load_model
from datetime import datetime
from pathlib import Path
from sklearn.preprocessing import MinMaxScaler, StandardScaler, RobustScaler, Normalizer, MaxAbsScaler
from sklearn.impute import SimpleImputer
from sklearn.model_selection import train_test_split
from sklearn.metrics import confusion_matrix
import seaborn as sns
import matplotlib.pyplot as plt
import joblib
import json
import traceback
from DataStore import DataStore
# Setup paths
script_dir = Path(__file__).resolve().parent
project_root = script_dir.parent.parent  # Adjusted to reach the project root
utilities_dir = project_root / 'Scripts' / 'Utilities'

# Add the Utilities directory to sys.path
if utilities_dir.exists() and str(utilities_dir) not in sys.path:
    sys.path.append(str(utilities_dir))

# Print sys.path for debugging
print("Updated sys.path:")
for p in sys.path:
    print(p)

# Now attempt to import modules
try:
    from config_handling import ConfigManager

except ImportError as e:
    print(f"Error importing modules: {e}")
    sys.exit(1)

def check_for_nan_inf(data):
    if np.isnan(data).any() or np.isinf(data).any():
        raise ValueError("Data contains NaN or infinite values.")
        
class LoggerHandler:
    def __init__(self, log_text_widget=None, logger=None):
        self.log_text_widget = log_text_widget
        self.logger = logger or logging.getLogger(__name__)

    def log(self, message, level="INFO"):
        if self.log_text_widget:
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            formatted_message = f"[{timestamp} - {level}] {message}\n"
            self.log_text_widget.config(state='normal')
            self.log_text_widget.insert('end', formatted_message)
            self.log_text_widget.config(state='disabled')
            self.log_text_widget.see('end')
        else:
            if level == "INFO":
                self.logger.info(message)
            elif level == "DEBUG":
                self.logger.debug(message)
            elif level == "WARNING":
                self.logger.warning(message)
            elif level == "ERROR":
                self.logger.error(message)
            else:
                self.logger.log(logging.INFO, message)

    def error(self, message, exc_info=False):
        self.logger.error(message, exc_info=exc_info)


class DataLoader:
    def __init__(self, logger_handler):
        self.logger = logger_handler

    def load_data(self, file_path):
        try:
            data = pd.read_csv(file_path)
            return data
        except FileNotFoundError:
            error_message = f"No such file or directory: '{file_path}'"
            self.logger.error(error_message)
            return None

    def save_scaler(self, scaler, file_path):
        joblib.dump(scaler, file_path)
        self.logger.log(f"Scaler saved to {file_path}.")

    def load_scaler(self, file_path):
        try:
            scaler = joblib.load(file_path)
            self.logger.log(f"Scaler loaded from {file_path}.")
            return scaler
        except Exception as e:
            self.logger.log(f"Failed to load scaler from {file_path}: {str(e)}", "ERROR")
            return None

    def save_metadata(self, metadata, file_path):
        with open(file_path, 'w') as metadata_file:
            json.dump(metadata, metadata_file, indent=4)
        self.logger.log(f"Metadata saved to {file_path}.")

    def load_metadata(self, file_path):
        try:
            with open(file_path, 'r') as metadata_file:
                metadata = json.load(metadata_file)
            self.logger.log(f"Metadata loaded from {file_path}.")
            return metadata
        except Exception as e:
            self.logger.log(f"Failed to load metadata from {file_path}: {str(e)}", "ERROR")
            return None


class DataPreprocessor:
    def __init__(self, logger_handler, config_manager):
        self.logger = logger_handler
        self.config_manager = config_manager
        self.scalers = {
            'StandardScaler': StandardScaler(),
            'MinMaxScaler': MinMaxScaler(),
            'RobustScaler': RobustScaler(),
            'Normalizer': Normalizer(),
            'MaxAbsScaler': MaxAbsScaler()
        }

    def handle_missing_values(self, data):
        # Step 1: Forward fill missing values
        data_filled = data.fillna(method='ffill')

        # Step 2: Apply rolling mean only to numeric columns to handle any remaining missing values
        numeric_cols = data_filled.select_dtypes(include=[np.number]).columns
        data_filled[numeric_cols] = data_filled[numeric_cols].fillna(data_filled[numeric_cols].rolling(window=5, min_periods=1).mean())

        # Step 3: Fill remaining NaN values with the mean of each numeric column
        data_filled[numeric_cols] = data_filled[numeric_cols].fillna(data_filled[numeric_cols].mean())

        return data_filled

    def _handle_dates(self, X, date_column):
        if date_column in X.columns:
            X[date_column] = pd.to_datetime(X[date_column])
            X.set_index(date_column, inplace=True)
            self.logger.log(f"Date column {date_column} processed and set as index.", "INFO")
        else:
            self.logger.log(f"Date column {date_column} not found in data.", "WARNING")
        return X

    def _create_lag_features(self, data, target_column, lag_sizes):
        for lag in lag_sizes:
            data[f'{target_column}_lag_{lag}'] = data[target_column].shift(lag)
        return data

    def _create_window_features(self, data, target_column, window_sizes):
        for window in window_sizes:
            data[f'{target_column}_rolling_mean_{window}'] = data[target_column].rolling(window=window).mean()
            data[f'{target_column}_rolling_std_{window}'] = data[target_column].rolling(window=window).std()
        return data


    def preprocess_data(self, data, model_type, expected_num_features=None, time_steps=1):
        """
        Preprocesses the data to ensure it matches the expected input shape for the model type.

        Parameters:
        - data: numpy array, pandas DataFrame, or file path to CSV, the input data to preprocess.
        - model_type: str, the type of model ('lstm', 'neural_network', 'linear_regression', etc.).
        - expected_num_features: int, the expected number of features the model requires (optional if already known).
        - time_steps: int, the number of time steps to consider for time-series models like LSTM (default is 1).

        Returns:
        - Processed data depending on the model type.
        """
        try:
            # Load data from CSV if data is a file path
            if isinstance(data, str):
                data = pd.read_csv(data)

            # If data is a DataFrame, ensure proper handling of date columns
            if isinstance(data, pd.DataFrame):
                # Remove or convert date columns
                if 'date' in data.columns:
                    data = data.drop(columns=['date'])  # Drop the date column or transform it as needed

                if 'symbol' in data.columns:
                    data = data.drop(columns(['symbol']))  # Drop the symbol column if it exists

                # Handle missing values by filling with the median of each column
                imputer = SimpleImputer(strategy='median')
                data = pd.DataFrame(imputer.fit_transform(data), columns=data.columns)

                # Convert DataFrame to NumPy array for consistency
                data = data.values  

            if model_type in ['lstm', 'neural_network']:
                # Automatically determine the number of features if not provided
                if expected_num_features is None:
                    expected_num_features = data.shape[1]

                if data.shape[1] != expected_num_features:
                    raise ValueError(f"Input data has {data.shape[1]} features, but the model expects {expected_num_features} features.")

                if model_type == 'lstm':
                    data = data.reshape((data.shape[0] // time_steps, time_steps, expected_num_features))
                elif model_type == 'neural_network':
                    data = data.reshape(-1, expected_num_features)

            elif model_type == 'linear_regression':
                if data.shape[1] < 2:
                    raise ValueError("Input data must have at least two columns: features and target.")

                X = data[:, :-1]  # All columns except the last are features
                y = data[:, -1]   # The last column is the target

                X_train, X_val, y_train, y_val = train_test_split(X, y, test_size=0.2, random_state=42)
                return X_train, X_val, y_train, y_val

        except Exception as e:
            error_message = f"Error during data preprocessing for model type '{model_type}': {str(e)}"
            self.logger.error(error_message, exc_info=True)
            return None

class VisualizationHandler:
    def __init__(self, logger_handler):
        self.logger = logger_handler

    def plot_confusion_matrix(self, y_true=None, y_pred=None, conf_matrix=None, class_names=None, save_path="confusion_matrix.png", show_plot=True):
        if conf_matrix is None:
            if y_true is None or y_pred is None:
                self.logger.log("You must provide either a confusion matrix or true and predicted labels.", "ERROR")
                return
            conf_matrix = confusion_matrix(y_true, y_pred)

        if class_names is None:
            class_names = list(range(conf_matrix.shape[0]))

        plt.figure(figsize=(8, 6))
        sns.heatmap(conf_matrix, annot=True, fmt='d', cmap='Blues', xticklabels=class_names, yticklabels=class_names, cbar=False)
        plt.xlabel('Predicted Labels')
        plt.ylabel('True Labels')
        plt.title('Confusion Matrix')

        if save_path:
            plt.savefig(save_path)

        if show_plot:
            plt.show()
        else:
            plt.close()

        self.logger.log(f"Confusion matrix plot saved to {save_path}.")


# Example utility functions
def setup_logger(name, log_file=None, level=logging.DEBUG):
    logger = logging.getLogger(name)
    logger.setLevel(level)
    ch = logging.StreamHandler()
    ch.setLevel(level)
    formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
    ch.setFormatter(formatter)
    logger.addHandler(ch)
    
    if log_file:
        fh = logging.FileHandler(log_file)
        fh.setLevel(level)
        fh.setFormatter(formatter)
        logger.addHandler(fh)
    
    return logger


def detect_models(model_dir):
    """Detect available models in the specified directory."""
    model_types = ['arima', 'lstm', 'neural_network', 'random_forest', 'linear_regression']
    detected_models = {}
    
    for model_type in model_types:
        model_files = list(Path(model_dir).rglob(f"*{model_type}*"))
        if model_files:
            detected_models[model_type] = str(model_files[0])  # Take the first found model
    
    return detected_models


def load_model_from_file(model_type, model_path, logger):
    try:
        # Implement model loading logic here, e.g., using joblib, pickle, etc.
        model = joblib.load(model_path)  # Example; replace with actual model loading
        logger.info(f"Successfully loaded {model_type} model from {model_path}.")
        return model
    except Exception as e:
        logger.error(f"Failed to load {model_type} model from {model_path}: {str(e)}")
        return None


def preprocess_data(data, model_type, expected_num_features=None, time_steps=1, logger=None):
    """
    Preprocesses the data to ensure it matches the expected input shape for the specified model type.

    Parameters:
    - data: numpy array, pandas DataFrame, or file path to CSV, the input data to preprocess.
    - model_type: str, the type of model ('lstm', 'neural_network', 'cnn', 'transformer', etc.).
    - expected_num_features: int, the expected number of features the model requires (optional if already known).
    - time_steps: int, the number of time steps to consider for time-series models like LSTM (default is 1).
    - logger: Logger object, used for logging errors (optional).

    Returns:
    - data: Preprocessed data reshaped for the specific model type.
    - None: if an error occurs during preprocessing.
    """
    try:
        # Load data from CSV if data is a file path
        if isinstance(data, str):
            if os.path.exists(data):
                data = pd.read_csv(data)
            else:
                raise ValueError(f"Provided string is not a valid file path: {data}")

        # If data is a DataFrame, ensure proper handling of date columns
        if isinstance(data, pd.DataFrame):
            # Remove or convert date columns
            if 'date' in data.columns:
                data = data.drop(columns=['date'])  # Drop the date column or transform it as needed

            if 'symbol' in data.columns:
                data = data.drop(columns=['symbol'])  # Drop the symbol column if it exists

            # Handle missing values by filling with the median of each column
            imputer = SimpleImputer(strategy='median')
            data = pd.DataFrame(imputer.fit_transform(data), columns=data.columns)

            # Convert DataFrame to NumPy array for consistency
            data = data.values  

        # Check if the data is a numpy array after possible conversion
        if not isinstance(data, np.ndarray):
            raise ValueError("Data should be a numpy array or pandas DataFrame")

        # Model-specific preprocessing
        if model_type in ['lstm', 'neural_network']:
            # Automatically determine the number of features if not provided
            if expected_num_features is None:
                expected_num_features = data.shape[1]

            if data.shape[1] != expected_num_features:
                raise ValueError(f"Input data has {data.shape[1]} features, but the model expects {expected_num_features} features.")

            if model_type == 'lstm':
                data = data.reshape((data.shape[0] // time_steps, time_steps, expected_num_features))
            elif model_type == 'neural_network':
                data = data.reshape(-1, expected_num_features)

        elif model_type == 'linear_regression':
            if data.shape[1] < 2:
                raise ValueError("Input data must have at least two columns: features and target.")

            X = data[:, :-1]  # All columns except the last are features
            y = data[:, -1]   # The last column is the target

            X_train, X_val, y_train, y_val = train_test_split(X, y, test_size=0.2, random_state=42)
            return X_train, X_val, y_train, y_val

        return data

    except Exception as e:
        error_message = f"Error during data preprocessing for model type '{model_type}': {str(e)}"
        if logger:
            logger.error(error_message, exc_info=True)
        else:
            print(error_message)
        return None


def save_predictions(predictions, model_type, output_dir, format='parquet', compress=True):
    predictions_df = pd.DataFrame(predictions)
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    subdir = os.path.join(output_dir, model_type, timestamp)
    os.makedirs(subdir, exist_ok=True)

    if format == 'csv':
        output_path = os.path.join(subdir, f"{model_type}_predictions.csv")
        predictions_df.to_csv(output_path, index=False)
    elif format == 'json':
        output_path = os.path.join(subdir, f"{model_type}_predictions.json")
        predictions_df.to_json(output_path, orient='records')
    elif format == 'parquet':
        output_path = os.path.join(subdir, f"{model_type}_predictions.parquet")
        predictions_df.to_parquet(output_path, index=False, compression='gzip' if compress else None)
    else:
        raise ValueError(f"Unsupported format: {format}")

    return output_path

# Function to calculate RMSE
def root_mean_squared_error(y_true, y_pred):
    return np.sqrt(mean_squared_error(y_true, y_pred))


def save_metadata(output_dir, model_type, model_path, input_data_path, prediction_path, logger=None):
    metadata = {
        "model_type": model_type,
        "model_path": model_path,
        "input_data_path": input_data_path,
        "prediction_path": prediction_path,
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    metadata_df = pd.DataFrame([metadata])
    summary_file = os.path.join(output_dir, "output_summary.csv")

    if os.path.exists(summary_file):
        existing_df = pd.read_csv(summary_file)
        metadata_df = pd.concat([existing_df, metadata_df])

    metadata_df.to_csv(summary_file, index=False)
    if logger:
        logger.info(f"Metadata saved to {summary_file}")


def validate_predictions(predictions, logger=None):
    if any(pred is None or np.isnan(pred).any() for pred in predictions.values()):
        if logger:
            logger.warning("Some predictions contain NaN or None values.")
    else:
        if logger:
            logger.info("All predictions are valid.")


def create_sequences(data, target, time_steps=10):
    sequences = []
    targets = []
    for i in range(len(data) - time_steps):
        sequences.append(data[i:i + time_steps])
        targets.append(target[i + time_steps])
    return np.array(sequences), np.array(targets)


def prepare_data(data, target_column='close', time_steps=10):
    if not isinstance(data, pd.DataFrame):
        raise ValueError("Input data must be a pandas DataFrame")
    
    if target_column not in data.columns:
        raise ValueError(f"Target column '{target_column}' not found in data")
    
    data = data.copy()
    numeric_data = data.select_dtypes(include=[np.number])
    scaler = MinMaxScaler()
    scaled_data = scaler.fit_transform(numeric_data)
    
    target = data[target_column].values
    sequences, targets = create_sequences(scaled_data, target, time_steps)
    
    return sequences, targets, scaler


def load_config(config_file):
    """Function to load a YAML configuration file."""
    with open(config_file, 'r') as file:
        config = yaml.safe_load(file)
    return config


def get_project_root():
    """Return the project root path based on current file location."""
    return Path(__file__).resolve().parent.parent

# Example of how to use these classes:
if __name__ == "__main__":
    logger_handler = LoggerHandler()
    data_loader = DataLoader(logger_handler)
    config_manager = ConfigManager()
    data_preprocessor = DataPreprocessor(logger_handler, config_manager)
    visualization_handler = VisualizationHandler(logger_handler)
    
    # Set the correct file path for your data
    file_path = 'C:/TheTradingRobotPlugWeb/data/alphavantage/raw/tsla/tsla_data.csv'

    # Check if the file path is valid
    if not os.path.exists(file_path):
        raise FileNotFoundError(f"Provided string is not a valid file path: {file_path}")

    preprocessed_data = preprocess_data(file_path, 'linear_regression')
