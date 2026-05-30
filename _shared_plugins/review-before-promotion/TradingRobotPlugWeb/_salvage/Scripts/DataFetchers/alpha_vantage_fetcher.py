# alpha_vantage_fetcher.py
# C:\TheTradingRobotPlugWeb\my_custom_theme\Data_Fetchers\alpha_vantage_fetcher.py
# Description:
# This script fetches stock data from the Alpha Vantage API and processes it for storage in a database.
# It handles both historical and real-time data, and includes functionalities for API interaction,
# data extraction, and conversion to pandas DataFrames.
# It also supports asynchronous data fetching to enhance performance and handle large volumes of data.
# The script includes comprehensive logging for tracking and debugging purposes.

import asyncio
import logging
import os
import sys
from pathlib import Path
from typing import Optional, List, Dict, Any
import pandas as pd
from aiohttp import ClientSession, ClientTimeout, ClientConnectionError, ContentTypeError
from mysql.connector import Error
import mysql

# Correct project root detection
script_dir = os.path.dirname(os.path.abspath(__file__))
project_root = os.path.abspath(os.path.join(script_dir, os.pardir, os.pardir))
expected_project_root = "C:\\TheTradingRobotPlugWeb"

if project_root != expected_project_root:
    project_root = expected_project_root
    print(f"Corrected Project root path: {project_root}")

# Set up relative paths for resources, logs, and database
resources_path = os.path.join(project_root, 'data', 'alpha_vantage', 'raw')
processed_path = os.path.join(project_root, 'data', 'alpha_vantage', 'processed')
log_path = os.path.join(project_root, 'logs')
db_path = os.path.join(project_root, 'database', 'trading_data.db')

# Ensure the directories exist
os.makedirs(resources_path, exist_ok=True)
os.makedirs(processed_path, exist_ok=True)
os.makedirs(log_path, exist_ok=True)

# Logging configuration
log_file_name = os.path.splitext(os.path.basename(__file__))[0] + '.log'
log_file = os.path.join(log_path, log_file_name)
logging.basicConfig(
    filename=log_file, 
    level=os.getenv('LOG_LEVEL', 'DEBUG'), 
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# Conditional imports based on execution context
try:
    from Scripts.Utilities.data_fetch_utils import DataFetchUtils
    from Scripts.DataFetchers.data_fetcher import DataFetcher
    from Scripts.Utilities.DataLakeHandler import DataLakeHandler
except ImportError:
    from unittest.mock import Mock as DataFetchUtils
    from unittest.mock import Mock as DataFetcher
    from unittest.mock import Mock as DataLakeHandler