# File: custom_indicators.py
# Location: Scripts/Technical_Indicators
# Description: This script provides custom technical indicators with caching support for trading algorithms.

import os
import sys
import logging
import pandas as pd
import joblib
from typing import Callable, List, Tuple, Dict, Any
from time import time as timer
from logging.handlers import RotatingFileHandler  # Corrected import

# Add project root to the Python path
script_dir = os.path.dirname(os.path.abspath(__file__))
project_root = os.path.abspath(os.path.join(script_dir, os.pardir, os.pardir))
sys.path.append(project_root)

# Set up relative paths for data and logs
data_path = os.path.join(project_root, 'data')
log_path = os.path.join(project_root, 'logs')

# Ensure the directories exist
os.makedirs(data_path, exist_ok=True)
os.makedirs(log_path, exist_ok=True)

# Logging configuration with log rotation
log_file = os.path.join(log_path, 'custom_indicators.log')
handler = RotatingFileHandler(log_file, maxBytes=5*1024*1024, backupCount=3)
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s', handlers=[handler])
logger = logging.getLogger(__name__)

# Conditional imports based on execution context
try:
    from Scripts.Utilities.config_handling import ConfigManager
except ImportError:
    from unittest.mock import Mock as ConfigManager

class CustomIndicators:
    """
    A class providing custom technical indicators with caching support.
    This class supports both memory and file-based caching strategies.
    """
    _cache = {}
    config_manager = ConfigManager()  # Initialize ConfigManager here

    @staticmethod
    def file_cache(key: str, function: Callable, *args: Any, **kwargs: Any) -> pd.Series:
        """
        Caches the result of a function call to a file, and returns the cached result if available.

        Args:
            key (str): Unique identifier for the cache.
            function (Callable): The function to cache.
            *args (Any): Arguments to pass to the function.
            **kwargs (Any): Keyword arguments to pass to the function.

        Returns:
            pd.Series: The result of the function, either cached or freshly computed.
        """
        cache_file = os.path.join(data_path, 'cache', f"{key}.pkl")
        if os.path.exists(cache_file):
            logger.info(f"Loading from file cache: {cache_file}")
            return joblib.load(cache_file)
        result = function(*args, **kwargs)
        os.makedirs(os.path.dirname(cache_file), exist_ok=True)
        joblib.dump(result, cache_file)
        logger.info(f"Cached result to file: {cache_file}")
        return result

    @staticmethod
    def get_cache_key(df: pd.DataFrame, function_name: str, args: Tuple, kwargs: Dict) -> str:
        """
        Generates a unique cache key based on the function name, DataFrame, arguments, and keyword arguments.

        Args:
            df (pd.DataFrame): The DataFrame being processed.
            function_name (str): The name of the function being cached.
            args (Tuple): The positional arguments passed to the function.
            kwargs (Dict): The keyword arguments passed to the function.

        Returns:
            str: A unique cache key.
        """
        df_hash = joblib.hash(df.to_string())
        args_hash = joblib.hash((args, frozenset(kwargs.items())))
        return f"{function_name}_{df_hash}_{args_hash}"

    @staticmethod
    def memory_cache(key: str, function: Callable, *args: Any, **kwargs: Any) -> pd.Series:
        """
        Caches the result of a function call in memory, and returns the cached result if available.

        Args:
            key (str): Unique identifier for the cache.
            function (Callable): The function to cache.
            *args (Any): Arguments to pass to the function.
            **kwargs (Any): Keyword arguments to pass to the function.

        Returns:
            pd.Series: The result of the function, either cached or freshly computed.
        """
        if key not in CustomIndicators._cache:
            CustomIndicators._cache[key] = function(*args, **kwargs)
            logger.info(f"Cached result in memory under key: {key}")
        return CustomIndicators._cache[key]

    @staticmethod
    def cached_indicator_function(
        df: pd.DataFrame, 
        indicator_function: Callable, 
        *args: Any, 
        cache_strategy: str = 'memory', 
        **kwargs: Any
    ) -> pd.Series:
        """
        Applies a technical indicator function to a DataFrame with caching support.

        Args:
            df (pd.DataFrame): The DataFrame to which the indicator will be applied.
            indicator_function (Callable): The technical indicator function to apply.
            *args (Any): Arguments to pass to the indicator function.
            cache_strategy (str): Caching strategy ('memory' or 'file'). Defaults to 'memory'.
            **kwargs (Any): Keyword arguments to pass to the indicator function.

        Returns:
            pd.Series: The result of the indicator function, possibly retrieved from cache.
        """
        cache_key = CustomIndicators.get_cache_key(df, indicator_function.__name__, args, kwargs)
        
        if cache_strategy == 'memory':
            return CustomIndicators.memory_cache(cache_key, indicator_function, df, *args, **kwargs)
        elif cache_strategy == 'file':
            return CustomIndicators.file_cache(cache_key, indicator_function, df, *args, **kwargs)
        else:
            raise ValueError(f"Unknown cache strategy: {cache_strategy}")

    @staticmethod
    def add_custom_indicator(
        df: pd.DataFrame, 
        indicator_name: str, 
        indicator_function: Callable, 
        *args: Any, 
        **kwargs: Any
    ) -> pd.DataFrame:
        """
        Adds a custom technical indicator to a DataFrame, with optional caching.

        Args:
            df (pd.DataFrame): The DataFrame to which the indicator will be added.
            indicator_name (str): The name of the indicator.
            indicator_function (Callable): The function to calculate the indicator.
            *args (Any): Arguments to pass to the indicator function.
            **kwargs (Any): Keyword arguments to pass to the indicator function.

        Returns:
            pd.DataFrame: The DataFrame with the new indicator added.
        """
        if not isinstance(df, pd.DataFrame):
            raise ValueError("The input 'df' must be a pandas DataFrame.")
        if not callable(indicator_function):
            raise ValueError("'indicator_function' must be a callable function.")
        if not isinstance(indicator_name, str) or not indicator_name:
            raise ValueError("'indicator_name' must be a non-empty string.")

        logger.info(f"Adding custom indicator '{indicator_name}' to the DataFrame.")
        try:
            start_time = timer()

            # Use the ConfigManager's get method with a default value
            indicator_params = CustomIndicators.config_manager.get('INDICATORS', {}).get(indicator_name, None)
            if indicator_params:
                if isinstance(indicator_params, str):
                    indicator_params = eval(indicator_params)
                if isinstance(indicator_params, dict):
                    kwargs.update(indicator_params)

            # Retrieve the cache strategy
            cache_strategy = CustomIndicators.config_manager.get('CACHE', {}).get('strategy', 'memory')

            df[indicator_name] = CustomIndicators.cached_indicator_function(
                df, indicator_function, *args, cache_strategy=cache_strategy, **kwargs
            )
            end_time = timer()
            logger.info(f"Successfully added custom indicator '{indicator_name}' in {end_time - start_time:.2f} seconds.")
        except Exception as e:
            logger.error(f"Error in executing the custom indicator function '{indicator_name}': {e}")
            raise RuntimeError(f"Error in executing the custom indicator function: {e}")

        return df
