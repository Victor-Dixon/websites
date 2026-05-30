import unittest
from unittest.mock import patch, AsyncMock, MagicMock
import logging
import asyncio
import sys
from pathlib import Path

# Ensure the project root is in the Python path for module imports
script_dir = Path(__file__).resolve().parent
project_root = script_dir.parent.parent.parent  # Adjust this based on your project structure
sys.path.append(str(project_root))

from Scripts.Data_Fetchers.API_interaction import BaseAPI, AlphaVantageAPI

class TestBaseAPI(unittest.TestCase):

    @patch('Scripts.Data_Fetchers.API_interaction.os.getenv', return_value='test_api_key')
    def test_base_api_initialization(self, mock_getenv):
        base_url = 'https://example.com/api'
        api = BaseAPI(base_url)
        self.assertEqual(api.base_url, base_url)
        self.assertEqual(api.api_key, 'test_api_key')
        self.assertIsInstance(api.logger, logging.Logger)

    def test_construct_url_not_implemented(self):
        base_url = 'https://example.com/api'
        api = BaseAPI(base_url)
        with self.assertRaises(NotImplementedError):
            api._construct_url('AAPL', 'daily')

    @patch('Scripts.Data_Fetchers.API_interaction.asyncio.sleep', new_callable=AsyncMock)
    @patch.object(BaseAPI, 'async_fetch_data', new_callable=AsyncMock)
    def test_handle_rate_limit(self, mock_fetch_data, mock_sleep):
        base_url = 'https://example.com/api'
        api = BaseAPI(base_url)

        # Mock the return value of async_fetch_data to None (simulate rate limit failure)
        mock_fetch_data.return_value = None

        # Run the rate limit handler
        result = asyncio.run(api.handle_rate_limit())

        # Ensure it retried 5 times
        self.assertEqual(mock_fetch_data.call_count, 5)
        self.assertIsNone(result)


class TestAlphaVantageAPI(unittest.TestCase):

    def setUp(self):
        self.base_url = 'https://www.alphavantage.co/query'
        self.api = AlphaVantageAPI(self.base_url)

    @patch('Scripts.Data_Fetchers.API_interaction.os.getenv', return_value='test_api_key')
    def test_alpha_vantage_api_initialization(self, mock_getenv):
        api = AlphaVantageAPI(self.base_url)
        self.assertEqual(api.base_url, self.base_url)
        self.assertEqual(api.api_key, 'test_api_key')
        self.assertIsInstance(api.logger, logging.Logger)

    def test_construct_url(self):
        symbol = 'AAPL'
        interval = 'daily'
        expected_url = f"{self.base_url}?function=TIME_SERIES_DAILY&symbol=AAPL&apikey=test_api_key&outputsize=full&datatype=json"
        self.api.api_key = 'test_api_key'
        result = self.api._construct_url(symbol, interval)
        self.assertEqual(result, expected_url)

    @patch('Scripts.Data_Fetchers.API_interaction.aiohttp.ClientSession', new_callable=AsyncMock)
    async def test_async_fetch_data(self, mock_client_session):
        symbol = 'AAPL'
        interval = 'daily'

        # Mock the async context manager
        mock_response = MagicMock()
        mock_response.status = 200
        mock_response.json.return_value = {'Meta Data': 'data'}

        # Mock the session's 'get' method to return a response object
        mock_client_session.return_value.__aenter__.return_value.get.return_value.__aenter__.return_value = mock_response

        result = await self.api.async_fetch_data(symbol, interval)

        self.assertIsNotNone(result)
        self.assertIn('Meta Data', result)

    @patch('Scripts.Data_Fetchers.API_interaction.aiohttp.ClientSession', new_callable=AsyncMock)
    async def test_async_fetch_data_rate_limit(self, mock_client_session):
        symbol = 'AAPL'
        interval = 'daily'

        # Mock the async context manager
        mock_response = MagicMock()
        mock_response.status = 429  # Simulate rate limit
        mock_client_session.return_value.__aenter__.return_value.get.return_value.__aenter__.return_value = mock_response

        with patch.object(self.api, 'handle_rate_limit', return_value={'Meta Data': 'data'}) as mock_handle_rate_limit:
            result = await self.api.async_fetch_data(symbol, interval)

            self.assertIsNotNone(result)
            self.assertIn('Meta Data', result)
            mock_handle_rate_limit.assert_called_once()

if __name__ == '__main__':
    unittest.main()
