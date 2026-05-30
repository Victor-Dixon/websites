import unittest
from unittest.mock import patch, MagicMock
import requests
import pandas as pd
from pathlib import Path
import sys

# Ensure the project root is in the Python path for module imports
script_dir = Path(__file__).resolve().parent
project_root = script_dir.parent.parent.parent  # Adjust this based on your project structure
sys.path.append(str(project_root))

from Scripts.Data_Fetchers.real_time_fetcher import RealTimeDataFetcher

class TestRealTimeDataFetcher(unittest.TestCase):

    def setUp(self):
        # Set up the RealTimeDataFetcher instance with mock API keys
        self.fetcher = RealTimeDataFetcher(alpha_api_key="mock_alpha_key", polygon_api_key="mock_polygon_key")

    def test_construct_alpha_api_url(self):
        # Test the construction of the Alpha Vantage API URL
        symbol = "AAPL"
        expected_url = (
            f"https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY"
            f"&symbol={symbol}&interval=1min&apikey=mock_alpha_key"
        )
        url = self.fetcher.construct_alpha_api_url(symbol)
        self.assertEqual(url, expected_url)

    def test_construct_polygon_api_url(self):
        # Test the construction of the Polygon API URL
        symbol = "AAPL"
        expected_url = f"https://api.polygon.io/v1/last/stocks/AAPL?apiKey=mock_polygon_key"
        url = self.fetcher.construct_polygon_api_url(symbol)
        self.assertEqual(url, expected_url)

    @patch("requests.get")
    def test_fetch_real_time_data_alpha_vantage(self, mock_get):
        # Mock the Alpha Vantage API response
        mock_get.return_value.status_code = 200
        mock_get.return_value.json.return_value = {
            "Time Series (1min)": {
                "2023-01-01 09:30:00": {
                    "1. open": "150.00",
                    "2. high": "151.00",
                    "3. low": "149.00",
                    "4. close": "150.50",
                    "5. volume": "1000"
                }
            }
        }

        # Test fetching real-time data using Alpha Vantage
        df = self.fetcher.fetch_real_time_data("AAPL")

        # Rename columns to simplify access and convert values to float
        df.columns = df.columns.str.extract(r'\d+\.\s*(.*)')[0]
        df = df.astype(float, errors='ignore')  # Convert to float where possible

        self.assertIsInstance(df, pd.DataFrame)
        self.assertFalse(df.empty)
        self.assertEqual(df.iloc[0]["open"], 150.00)
        self.assertEqual(df.iloc[0]["high"], 151.00)

    @patch("requests.get")
    def test_fetch_real_time_data_polygon_fallback(self, mock_get):
        # Mock the Alpha Vantage API rate limit response and the Polygon API response
        mock_get.side_effect = [
            MagicMock(status_code=200, json=lambda: {"Information": "Alpha Vantage rate limit reached"}),
            MagicMock(status_code=200, json=lambda: {
                "results": [
                    {
                        "t": 1672531199000,
                        "o": 150.00,
                        "h": 151.00,
                        "l": 149.00,
                        "c": 150.50,
                        "v": 1000
                    }
                ]
            })
        ]

        # Test fetching real-time data with a fallback to Polygon
        df = self.fetcher.fetch_real_time_data("AAPL")
        
        self.assertIsInstance(df, pd.DataFrame)
        self.assertFalse(df.empty)
        self.assertEqual(df.iloc[0]["open"], 150.00)
        self.assertEqual(df.iloc[0]["high"], 151.00)

    @patch("requests.get")
    def test_fetch_real_time_data_both_fail(self, mock_get):
        # Mock both Alpha Vantage and Polygon API failures
        mock_get.side_effect = [
            MagicMock(status_code=200, json=lambda: {"Information": "Alpha Vantage rate limit reached"}),
            requests.exceptions.HTTPError("Polygon API request failed")
        ]

        # Test fetching real-time data when both APIs fail
        with self.assertRaises(RuntimeError):
            self.fetcher.fetch_real_time_data("AAPL")


if __name__ == "__main__":
    unittest.main()
