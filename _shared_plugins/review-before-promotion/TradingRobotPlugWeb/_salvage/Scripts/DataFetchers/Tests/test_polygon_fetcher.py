# File: test_polygon_fetcher.py
import os
import sys
import unittest
from unittest.mock import patch, AsyncMock, MagicMock
import pandas as pd
from pathlib import Path 
import asyncio
import datetime

# Ensure the project root is in the Python path for module imports
script_dir = Path(__file__).resolve().parent
project_root = script_dir.parent.parent.parent  # Adjust this based on your project structure
sys.path.append(str(project_root))

from Scripts.Data_Fetchers.polygon_fetcher import PolygonDataFetcher

class TestPolygonDataFetcher(unittest.TestCase):

    def setUp(self):
        # Set up the PolygonDataFetcher instance with mock dependencies
        self.data_lake_handler = MagicMock()
        self.fetcher = PolygonDataFetcher(data_lake_handler=self.data_lake_handler)
        self.fetcher = PolygonDataFetcher()
    def test_construct_api_url(self):
        # Test the URL construction
        symbol = "AAPL"
        start_date = "2023-01-01"
        end_date = "2023-12-31"
        expected_url = f"https://api.polygon.io/v2/aggs/ticker/AAPL/range/1/day/2023-01-01/2023-12-31?apiKey=POLYGON_API_KEY"
        url = self.fetcher.construct_api_url(symbol, start_date, end_date)
        self.assertEqual(url, expected_url)

    @patch('Scripts.Data_Fetchers.polygon_fetcher.PolygonDataFetcher.fetch_data', new_callable=AsyncMock)
    def test_fetch_real_time_data(self, mock_fetch_data):
        # Test fetching real-time data for a symbol
        symbol = "AAPL"
        mock_fetch_data.return_value = {
            'results': [
                {'t': 1672531199000, 'o': 150, 'h': 155, 'l': 149, 'c': 152, 'v': 1000000}
            ]
        }

        # Run the async fetch_real_time_data method
        result_df = asyncio.run(self.fetcher.fetch_real_time_data(symbol))

        # Check if the DataFrame was created correctly
        self.assertIsInstance(result_df, pd.DataFrame)
        self.assertFalse(result_df.empty)
        self.assertEqual(result_df.iloc[0]['open'], 150)
        self.assertEqual(result_df.iloc[0]['high'], 155)
        self.assertEqual(result_df.iloc[0]['low'], 149)
        self.assertEqual(result_df.iloc[0]['close'], 152)
        self.assertEqual(result_df.iloc[0]['volume'], 1000000)

    @patch('Scripts.Data_Fetchers.polygon_fetcher.PolygonDataFetcher.fetch_data_for_symbol', new_callable=AsyncMock)
    def test_fetch_data_for_multiple_symbols(self, mock_fetch_data_for_symbol):
        # Test fetching data for multiple symbols
        mock_fetch_data_for_symbol.return_value = pd.DataFrame({
            'open': [150],
            'high': [155],
            'low': [149],
            'close': [152],
            'volume': [1000000],
            'symbol': ['AAPL']
        })

        symbols = ["AAPL", "MSFT"]
        start_date = "2023-01-01"
        end_date = "2023-12-31"

        # Run the async fetch_data_for_multiple_symbols method
        results = asyncio.run(self.fetcher.fetch_data_for_multiple_symbols(symbols, start_date, end_date))

        # Check if the results are correct
        self.assertIn('AAPL', results)
        self.assertIn('MSFT', results)
        self.assertIsInstance(results['AAPL'], pd.DataFrame)
        self.assertIsInstance(results['MSFT'], pd.DataFrame)
        self.assertFalse(results['AAPL'].empty)
        self.assertEqual(results['AAPL'].iloc[0]['close'], 152)

    @patch('Scripts.Data_Fetchers.polygon_fetcher.PolygonDataFetcher.construct_api_url')
    @patch('Scripts.Data_Fetchers.polygon_fetcher.PolygonDataFetcher.fetch_data')
    @patch('Scripts.Data_Fetchers.polygon_fetcher.logging')  # Mock the logging module
    async def test_fetch_data_for_symbol(self, mock_logging, mock_fetch_data, mock_construct_api_url):
        # Prepare the mock data
        mock_construct_api_url.return_value = 'mocked_url'
        mock_fetch_data.return_value = {
            'results': [
                {'t': 1620086400000, 'o': 100.0, 'h': 110.0, 'l': 95.0, 'c': 105.0, 'v': 1000},
                {'t': 1620172800000, 'o': 105.0, 'h': 115.0, 'l': 100.0, 'c': 110.0, 'v': 1500}
            ]
        }
        mock_logging.debug = AsyncMock()
        mock_logging.warning = AsyncMock()
        mock_logging.error = AsyncMock()

        # Call the method under test
        result_df = await self.fetcher.fetch_data_for_symbol('AAPL', '2021-05-01', '2021-05-05')

        # Assert the results
        self.assertIsNotNone(result_df)
        self.assertEqual(len(result_df), 2)
        self.assertIn('symbol', result_df.columns)
        self.assertEqual(result_df.loc['2021-05-04']['close'], 105.0)

    @patch('Scripts.Data_Fetchers.polygon_fetcher.logging')
    def test_extract_results(self, mock_logging):
        # Prepare the mock data
        data = {
            'results': [
                {'t': 1620086400000, 'o': 100.0, 'h': 110.0, 'l': 95.0, 'c': 105.0, 'v': 1000},
                {'t': 1620172800000, 'o': 105.0, 'h': 115.0, 'l': 100.0, 'c': 110.0, 'v': 1500}
            ]
        }

        # Call the method under test
        results = self.fetcher.extract_results(data)

        # Assert the results
        self.assertEqual(len(results), 2)
        self.assertEqual(results[0]['date'], '2021-05-04')
        self.assertEqual(results[1]['close'], 110.0)

if __name__ == '__main__':
    unittest.main()
