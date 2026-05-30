# File: organize_data.py
# Location: C:\TheTradingRobotPlugWeb\scripts\model_training
# Description: Organizes the data files into a standardized directory structure.

import os
import shutil
from pathlib import Path
import logging
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

# Set up paths
script_dir = Path(__file__).resolve().parent
log_dir = script_dir / 'logs'

# Ensure the logs directory exists
os.makedirs(log_dir, exist_ok=True)

log_file = log_dir / 'organize_data.log'

# Set up logging
logging.basicConfig(level=logging.INFO, filename=log_file, filemode='w', format='%(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Base directory
base_dir = Path("C:/TheTradingRobotPlugWeb/data")

# Ensure the target directories exist
def ensure_directories(symbol):
    for data_type in ['alpha_vantage', 'polygon', 'real_time']:
        for category in ['raw', 'processed']:
            directory = base_dir / data_type / category / symbol
            os.makedirs(directory, exist_ok=True)
            logger.info(f"Ensured directory exists: {directory}")

# Organize files by symbol and date range
def organize_files():
    for data_type in ['alpha_vantage', 'polygon', 'real_time']:
        raw_dir = base_dir / data_type / 'raw'
        processed_dir = base_dir / data_type / 'processed'

        for file in raw_dir.glob('*.csv'):
            try:
                symbol, data_type, date_range = parse_filename(file.name)
                ensure_directories(symbol)

                # Move file to the appropriate directory
                destination = raw_dir / symbol / file.name
                shutil.move(str(file), str(destination))
                logger.info(f"Moved {file} to {destination}")

            except Exception as e:
                logger.error(f"Failed to organize file {file}: {e}")

        for file in processed_dir.glob('*.csv'):
            try:
                symbol, data_type, date_range = parse_filename(file.name)
                ensure_directories(symbol)

                # Move file to the appropriate directory
                destination = processed_dir / symbol / file.name
                shutil.move(str(file), str(destination))
                logger.info(f"Moved {file} to {destination}")

            except Exception as e:
                logger.error(f"Failed to organize file {file}: {e}")

def parse_filename(filename):
    """
    Parses a filename to extract the symbol, data type, and date range.
    Expected format: SYMBOL_data_YYYY-MM-DD_to_YYYY-MM-DD.csv
    """
    parts = filename.split('_')
    symbol = parts[0]
    data_type = parts[1]
    date_range = '_'.join(parts[2:]).replace('.csv', '')
    return symbol, data_type, date_range

if __name__ == "__main__":
    organize_files()
    logger.info("Data organization complete.")
