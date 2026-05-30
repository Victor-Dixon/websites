import unittest
from unittest.mock import patch, AsyncMock
from aioresponses import aioresponses
from aiohttp import ClientSession
import asyncio
from Scripts.Data_Fetchers.alpha_vantage_fetcher import AlphaVantageDataFetcher

class TestAlphaVantageDataFetcher(unittest.TestCase):

    @aioresponses()
    async def async_test_fetch_data_success(self, mocked):
        url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=MSFT&interval=1min&apikey=test_key&outputsize=full&datatype=json"
        mocked.get(url, payload={"Time Series (Daily)": {
            "2023-01-01": {
                "1. open": "100.00",
                "2. high": "110.00",
                "3. low": "90.00",
                "4. close": "105.00",
                "5. volume": "1000"
            }
        }})

        fetcher = AlphaVantageDataFetcher()

        async with ClientSession() as session:
            data = await fetcher.fetch_data(url, session)

        self.assertIn("Time Series (Daily)", data)
        self.assertEqual(data["Time Series (Daily)"]["2023-01-01"]["1. open"], "100.00")

    @aioresponses()
    async def async_test_fetch_data_failure(self, mocked):
        url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=MSFT&interval=1min&apikey=test_key&outputsize=full&datatype=json"
        mocked.get(url, status=500)

        fetcher = AlphaVantageDataFetcher()

        async with ClientSession() as session:
            with self.assertRaises(Exception):
                await fetcher.fetch_data(url, session)

    def test_fetch_data_success(self):
        asyncio.run(self.async_test_fetch_data_success())

    def test_fetch_data_failure(self):
        asyncio.run(self.async_test_fetch_data_failure())

if __name__ == "__main__":
    unittest.main()
