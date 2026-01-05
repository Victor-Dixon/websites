# File: automate_data_pipeline.py
# Location: C:\TheTradingRobotPlugWeb\my-custom-theme\automation\automate_data_pipeline.py
# Description: 
# This script automates the process of fetching real-time stock data, processing it, and storing both raw and processed data.
# It uses `RealTimeDataFetcher` to obtain the latest stock data and `DataStore` to save the data into storage.
# The script fetches data for a predefined list of stock symbols, cleans the data by dropping NaN values, and then saves 
# both the raw and cleaned versions of the data for each symbol.


def automate_data_pipeline():
    """
    Automates the data pipeline for fetching, processing, and storing stock data.
    
    This function initializes the data fetcher and data store, then iterates over a list of stock symbols to:
    1. Fetch real-time data using the RealTimeDataFetcher.
    2. Clean the data by removing rows with missing values.
    3. Save both the raw and cleaned data using the DataStore.
    """
    alpha_api_key = os.getenv('ALPHAVANTAGE_API_KEY')
    polygon_api_key = os.getenv('POLYGON_API_KEY')

    # Initialize the data fetcher and data store with API keys
    fetcher = RealTimeDataFetcher(alpha_api_key, polygon_api_key)
    store = DataStore()

    # List of stock symbols to fetch data for
    symbols = ['AAPL', 'GOOGL', 'MSFT']  # Example stock symbols

    for symbol in symbols:
        # Step 1: Fetch real-time data for the given symbol
        df = fetcher.fetch_real_time_data(symbol)

        # Step 2: Process data (e.g., cleaning, applying technical indicators)
        df_cleaned = df.dropna()  # Example of cleaning by dropping NaNs

        # Step 3: Save raw data to storage
        store.save_data(df, symbol, processed=False)

        # Step 4: Save processed data to storage
        store.save_data(df_cleaned, symbol, processed=True)

if __name__ == "__main__":
    automate_data_pipeline()
