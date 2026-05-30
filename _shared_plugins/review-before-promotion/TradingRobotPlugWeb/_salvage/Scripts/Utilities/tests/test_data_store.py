# File: test_data_store.py
# Location: C:\TheTradingRobotPlugWeb\Scripts\Utilities\Tests\
# Description: This test file verifies the functionality of the DataStore class in DataStore.py.

import unittest
from unittest.mock import patch, MagicMock, mock_open
import pandas as pd
import os
from pathlib import Path
from Scripts.Utilities.DataStore import DataStore

class TestDataStore(unittest.TestCase):

    def setUp(self):
        # Initialize the DataStore instance for testing
        self.csv_dir = 'C:/TheTradingRobotPlug/data/alpha_vantage'
        self.db_path = 'C:/TheTradingRobotPlug/data/trading_data.db'
        self.data_store = DataStore(csv_dir=self.csv_dir, db_path=self.db_path)

    def test_add_data(self):
        # Test adding data to the store
        ticker = 'AAPL'
        data = {'price': [150, 155, 160], 'volume': [1000, 1100, 1200]}

        self.data_store.add_data(ticker, data)
        self.assertIn(ticker, self.data_store.data)
        self.assertEqual(self.data_store.data[ticker], data)

    def test_get_data(self):
        # Test retrieving data from the store
        ticker = 'AAPL'
        data = {'price': [150, 155, 160], 'volume': [1000, 1100, 1200]}
        self.data_store.data[ticker] = data

        retrieved_data = self.data_store.get_data(ticker)
        self.assertEqual(retrieved_data, data)

    @patch('builtins.open', new_callable=mock_open)
    @patch('pickle.dump')
    def test_save_store(self, mock_pickle_dump, mock_open_file):
        # Test saving the data store to a file
        file_path = 'data_store.pkl'

        self.data_store.save_store(file_path)
        mock_open_file.assert_called_once_with(file_path, 'wb')
        mock_pickle_dump.assert_called_once_with(self.data_store.data, mock_open_file())

    @patch('builtins.open', new_callable=mock_open, read_data=b'')
    @patch('pickle.load')
    def test_load_store(self, mock_pickle_load, mock_open_file):
        # Test loading the data store from a file
        file_path = 'data_store.pkl'
        mock_data = {'AAPL': {'price': [150, 155, 160], 'volume': [1000, 1100, 1200]}}
        mock_pickle_load.return_value = mock_data

        self.data_store.load_store(file_path)
        mock_open_file.assert_called_once_with(file_path, 'rb')
        mock_pickle_load.assert_called_once_with(mock_open_file())
        self.assertEqual(self.data_store.data, mock_data)

    @patch('pandas.DataFrame.to_csv')
    def test_save_to_csv(self, mock_to_csv):
        # Test saving a DataFrame to a CSV file
        df = pd.DataFrame({'price': [150, 155, 160], 'volume': [1000, 1100, 1200]})
        file_name = 'AAPL_data.csv'

        self.data_store.save_to_csv(df, file_name)
        file_path = Path(self.csv_dir) / file_name
        mock_to_csv.assert_called_once_with(file_path, index=False)

    @patch('Scripts.Utilities.data_fetch_utils.DataFetchUtils.fetch_data_from_sql')
    def test_fetch_from_sql(self, mock_fetch_from_sql):
        # Test fetching data from an SQL table
        table_name = 'AAPL_table'
        mock_data = pd.DataFrame({'price': [150, 155, 160], 'volume': [1000, 1100, 1200]})
        mock_fetch_from_sql.return_value = mock_data

        df = self.data_store.fetch_from_sql(table_name)
        mock_fetch_from_sql.assert_called_once_with(table_name, Path(self.db_path))
        pd.testing.assert_frame_equal(df, mock_data)

    @patch('pandas.read_csv')
    def test_fetch_from_csv(self, mock_read_csv):
        # Test fetching data from a CSV file
        file_name = 'AAPL_data.csv'
        mock_data = pd.DataFrame({'price': [150, 155, 160], 'volume': [1000, 1100, 1200]})
        mock_read_csv.return_value = mock_data

        df = self.data_store.fetch_from_csv(file_name)
        file_path = Path(self.csv_dir) / file_name
        mock_read_csv.assert_called_once_with(file_path, parse_dates=['date'])
        pd.testing.assert_frame_equal(df, mock_data)

    def test_list_csv_files(self):
        # Test listing CSV files in a directory
        test_files = ['file1.csv', 'file2.csv', 'file3.csv']
        with patch.object(Path, 'glob', return_value=[Path(f) for f in test_files]):
            csv_files = self.data_store.list_csv_files()
            self.assertEqual(csv_files, test_files)

    @patch('pandas.read_csv')
    @patch('Scripts.Utilities.DataStore.DataStore.save_to_csv')
    def test_save_data(self, mock_save_to_csv, mock_read_csv):
        # Test saving data with versioning and archiving
        mock_data = pd.DataFrame({'price': [150, 155, 160], 'volume': [1000, 1100, 1200]})
        symbol = 'AAPL'
        processed = True
        versioning = True
        archive = True

        # Mock the CSV file listing to simulate versioning
        with patch.object(Path, 'exists', side_effect=[True, False]):
            self.data_store.save_data(mock_data, symbol, processed=processed, versioning=versioning, archive=archive)

        mock_save_to_csv.assert_called()
        archive_dir = Path(self.csv_dir) / 'archive'
        archive_path = archive_dir / f'{symbol}_data.csv'
        self.assertTrue(mock_save_to_csv.called)
        self.assertTrue(mock_read_csv.called)

if __name__ == '__main__':
    unittest.main()
