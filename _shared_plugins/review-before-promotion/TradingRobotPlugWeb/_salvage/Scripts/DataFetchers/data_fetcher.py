# File: data_fetcher.py
# Location: C:\TheTradingRobotPlugWeb\Scripts\Data_Fetchers\data_fetcher.py
# Description:
# This module contains the `DataFetcher` class, which is designed to fetch financial data from APIs,
# process it, and store it in various formats. The class supports asynchronous data fetching and 
# integrates with a data store and a data lake handler for efficient data management. It includes 
# functionality for logging errors and information during the data fetching process.
# The `DataFetcher` class is initialized with API keys, directories for raw and processed data, 
# and paths for the SQLite database and log file. It provides methods for fetching, processing, 
# and storing financial data. The class also includes a test function to demonstrate its usage.

import aiohttp
import pandas as pd
import logging
from typing import Optional, List, Dict
from dotenv import load_dotenv
import asyncio
from datetime import datetime, timedelta
import os
import sys

# Load environment variables from .env file
load_dotenv()

# Ensure the project root is in the Python path for module imports
script_dir = os.path.dirname(os.path.abspath(__file__))
project_root = os.path.abspath(os.path.join(script_dir, os.pardir, os.pardir))
sys.path.append(project_root)

from Scripts.Utilities.data_store import DataStore
from Scripts.Utilities.data_fetch_utils import DataFetchUtils
from Scripts.Utilities.DataLakeHandler import DataLakeHandler

class DataFetcher:
    """
    Base class for fetching financial data from APIs.

    Attributes:
        api_key (str): The API key for authentication.
        base_url (str): The base URL for the API.
        raw_csv_dir (str): Directory to store raw CSV files.
        processed_csv_dir (str): Directory to store processed CSV files.
        db_path (str): Path to the SQLite database.
        log_file (str): Path to the log file.
        source (str): The source of the data (e.g., AlphaVantage).
        data_lake_handler (Optional[object]): Handler for storing data in a data lake.
    """
    def __init__(self, api_key, base_url, raw_csv_dir, processed_csv_dir, db_path, log_file, source, data_lake_handler: Optional[object] = None):
        """
        Initializes the DataFetcher with the given parameters.
        
        Args:
            api_key (str): The API key for authentication.
            base_url (str): The base URL for the API.
            raw_csv_dir (str): Directory to store raw CSV files.
            processed_csv_dir (str): Directory to store processed CSV files.
            db_path (str): Path to the SQLite database.
            log_file (str): Path to the log file.
            source (str): The source of the data (e.g., AlphaVantage).
            data_lake_handler (Optional[object]): Handler for storing data in a data lake.
        """
        self.api_key = api_key
        self.base_url = base_url
        self.raw_csv_dir = raw_csv_dir
        self.processed_csv_dir = processed_csv_dir
        self.db_path = db_path
        self.log_file = log_file
        self.source = source
        self.data_lake_handler = data_lake_handler

        # Ensure directories exist
        os.makedirs(self.raw_csv_dir, exist_ok=True)
        os.makedirs(self.processed_csv_dir, exist_ok=True)

        # Initialize DataStore
        self.data_store = DataStore(self.raw_csv_dir, self.db_path)

        # Initialize logger
        self.logger = logging.getLogger(self.source)
        self.logger.setLevel(logging.DEBUG)
        handler = logging.FileHandler(self.log_file)
        handler.setFormatter(logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s'))
        self.logger.addHandler(handler)

        if not self.api_key:
            self.logger.error(f"{self.source}: API key not found in environment variables.")
            raise ValueError("API key not found in environment variables.")

    async def fetch_data(self, symbols: List[str], interval: str):
        """
        Fetches data for the given symbols and interval, and stores it.

        Args:
            symbols (List[str]): List of stock symbols.
            interval (str): The interval for data fetching.
        """
        for symbol in symbols:
            self.logger.info(f"Fetching data for {symbol}...")
            data = await self._async_fetch_data(symbol, interval)
            if data:
                self.logger.info(f"Processing data for {symbol}...")
                processed_data = self.process_data(data)
                self.store_data(processed_data, symbol)
                self.logger.info(f"Data for {symbol} stored successfully.")
            else:
                self.logger.error(f"Failed to fetch data for {symbol}")

    async def _async_fetch_data(self, symbol: str, interval: str) -> Optional[dict]:
        """
        Abstract method to fetch data asynchronously from the API.

        Args:
            symbol (str): The stock symbol to fetch data for.
            interval (str): The interval for data fetching.

        Returns:
            Optional[dict]: The fetched data as a dictionary, or None if fetching fails.
        """
        raise NotImplementedError

    def process_data(self, data: dict) -> pd.DataFrame:
        """
        Processes raw data into a DataFrame.

        Args:
            data (dict): The raw data to process.

        Returns:
            pd.DataFrame: The processed data as a DataFrame.
        """
        # Placeholder for data processing logic
        df = pd.DataFrame(data)
        return df

    def store_data(self, data: pd.DataFrame, symbol: str):
        """
        Stores the processed data.

        Args:
            data (pd.DataFrame): The data to store.
            symbol (str): The stock symbol associated with the data.
        """
        # Store to CSV
        csv_path = os.path.join(self.raw_csv_dir, f"{symbol}.csv")
        data.to_csv(csv_path, index=False)

        # Optionally store to database
        self.data_store.store_data(data, symbol)

        # Store in data lake
        if self.data_lake_handler:
            self.data_lake_handler.store_data(data, symbol)

# Test the DataFetcher separately
async def test_data_fetcher():
    """
    Tests the DataFetcher class by fetching data for a given symbol.
    """
    fetcher = DataFetcher(
        api_key=os.getenv('ALPHAVANTAGE_API_KEY'),
        base_url='https://www.alphavantage.co/query',
        raw_csv_dir='raw_data',
        processed_csv_dir='processed_data',
        db_path='data/database.db',
        log_file='logs/data_fetcher.log',
        source='AlphaVantage'
    )
    await fetcher.fetch_data(['AAPL'], 'daily')

# Uncomment the line below to test the DataFetcher directly
# asyncio.run(test_data_fetcher())
