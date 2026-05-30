# File: fetcher_gui.py
# Location: C:\TheTradingRobotPlug\Scripts\GUI\concepts\fetcher_gui.py
# Description: Provides a GUI for fetching data using Alpha Vantage and Polygon data fetchers.

import tkinter as tk
from tkinter import ttk, messagebox
import asyncio
import os
import sys
import logging
from datetime import datetime
from pathlib import Path

# Add project root to the Python path
script_dir = Path(__file__).resolve().parent
project_root = script_dir.parents[2]
sys.path.append(str(project_root))


from Scripts.GUI.concepts.base_gui import BaseGUI
from Scripts.Data_Fetchers.alpha_vantage_fetcher import AlphaVantageDataFetcher
from Scripts.Data_Fetchers.polygon_fetcher import PolygonDataFetcher

# Set up logging
log_file_path = os.path.join(project_root, 'logs', 'fetcher_gui.log')
logging.basicConfig(level=logging.DEBUG, filename=log_file_path, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class FetcherGUI(tk.Tk):
    def __init__(self):
        super().__init__()

        self.title("Data Fetcher")
        self.geometry("400x300")

        self.notebook = ttk.Notebook(self)
        self.notebook.pack(fill='both', expand=True)

        self.create_alpha_vantage_tab()
        self.create_polygon_tab()

    def create_alpha_vantage_tab(self):
        tab = ttk.Frame(self.notebook)
        self.notebook.add(tab, text="AlphaVantage Fetcher")

        ttk.Label(tab, text="AlphaVantage Data Fetcher").grid(row=0, column=0, padx=10, pady=10)
        ttk.Label(tab, text="Ticker Symbols (comma separated):").grid(row=1, column=0, padx=10, pady=5)
        self.alpha_tickers_entry = ttk.Entry(tab)
        self.alpha_tickers_entry.grid(row=1, column=1, padx=10, pady=5)

        ttk.Button(tab, text="Fetch Data", command=self.fetch_alpha_data).grid(row=2, column=0, columnspan=2, pady=10)

    def fetch_alpha_data(self):
        tickers = self.alpha_tickers_entry.get()
        if not tickers:
            messagebox.showwarning("Input Error", "Please enter ticker symbols.")
            return

        tickers_list = [ticker.strip() for ticker in tickers.split(",")]
        self.run_async_fetch(AlphaVantageDataFetcher(), tickers_list)

    def create_polygon_tab(self):
        tab = ttk.Frame(self.notebook)
        self.notebook.add(tab, text="Polygon Fetcher")

        ttk.Label(tab, text="Polygon Data Fetcher").grid(row=0, column=0, padx=10, pady=10)
        ttk.Label(tab, text="Ticker Symbols (comma separated):").grid(row=1, column=0, padx=10, pady=5)
        self.polygon_tickers_entry = ttk.Entry(tab)
        self.polygon_tickers_entry.grid(row=1, column=1, padx=10, pady=5)

        ttk.Button(tab, text="Fetch Data", command=self.fetch_polygon_data).grid(row=2, column=0, columnspan=2, pady=10)

    def fetch_polygon_data(self):
        tickers = self.polygon_tickers_entry.get()
        if not tickers:
            messagebox.showwarning("Input Error", "Please enter ticker symbols.")
            return

        tickers_list = [ticker.strip() for ticker in tickers.split(",")]
        self.run_async_fetch(PolygonDataFetcher(), tickers_list)

    def run_async_fetch(self, fetcher, tickers_list):
        """
        Runs the data fetching process asynchronously using asyncio.
        """
        try:
            asyncio.run(self.fetch_data_async(fetcher, tickers_list))
        except RuntimeError as e:
            messagebox.showerror("Runtime Error", str(e))
            logger.error(f"Runtime error during async fetch: {e}")

    async def fetch_data_async(self, fetcher, tickers_list):
        start_date = "2022-01-01"
        end_date = "2022-12-31"
        try:
            logger.debug(f"Starting data fetch for tickers: {tickers_list}")
            data = await fetcher.fetch_data(tickers_list, start_date, end_date)
            if data:
                messagebox.showinfo("Success", f"Data fetched for: {', '.join(data.keys())}")
                logger.info(f"Data successfully fetched for: {', '.join(data.keys())}")
            else:
                messagebox.showerror("Error", "Failed to fetch data.")
                logger.error("Failed to fetch data.")
        except Exception as e:
            messagebox.showerror("Error", str(e))
            logger.error(f"Error fetching data: {e}")

if __name__ == "__main__":
    app = FetcherGUI()
    app.mainloop()
