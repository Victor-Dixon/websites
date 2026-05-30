# File: config_handling.py
# Location: Scripts/Utilities/
# Description: This script provides utilities for managing configuration settings in a Python project. 
# It includes functionality to load configuration from a YAML file, substitute environment variables within the configuration, and retrieve configuration values. 
# Additionally, it offers methods to safely access environment variables and log configuration-related information for debugging and monitoring purposes.


import os
import yaml
import logging
from pathlib import Path
from dotenv import load_dotenv
from sklearn.metrics import mean_squared_error

# Load environment variables from .env file
load_dotenv()

# Initialize logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def substitute_env_vars(value):
    """
    Replace environment variable placeholders in the string.
    
    Args:
        value (str): The string with placeholders.
        
    Returns:
        str: The string with environment variables replaced.
    """
    return os.path.expandvars(value)

def get_env_value(key, default=None):
    """
    Retrieves the value of an environment variable, returning a default value if the variable is not set.

    Args:
        key (str): The name of the environment variable.
        default (str, optional): The value to return if the environment variable is not set.

    Returns:
        str: The value of the environment variable or the default value.
    """
    value = os.getenv(key, default)
    logger.debug(f"Retrieved environment variable '{key}' with value: '{value}'")
    return value

class ConfigManager:
    """
    Manages configuration settings from a YAML file and substitutes environment variables.
    """

    def __init__(self, config_file=None):
        """
        Initializes the ConfigManager, optionally loading a configuration file.

        Args:
            config_file (str, optional): The path to the configuration file.
        """
        self.config = {}
        if config_file:
            self.load_config_file(config_file)

    def load_config_file(self, config_file):
        """
        Loads and parses configuration from a YAML file.

        Args:
            config_file (str): The path to the configuration file.
        """
        try:
            with open(config_file, 'r') as file:
                file_config = yaml.safe_load(file) or {}
                # Substitute environment variables in the configuration
                self.config = {k: substitute_env_vars(v) for k, v in file_config.items()}
                logger.info(f"Configuration loaded from {config_file}: {self.config}")
        except FileNotFoundError:
            logger.error(f"Configuration file '{config_file}' not found.")
        except yaml.YAMLError as e:
            logger.error(f"Error parsing YAML file '{config_file}': {e}")
        except Exception as e:
            logger.error(f"Failed to load config file '{config_file}': {e}")

    def get(self, key, default=None):
        """
        Retrieves a value from the configuration.

        Args:
            key (str): The key for the desired configuration setting.
            default (any, optional): The default value if the key is not found.

        Returns:
            any: The configuration value or the default value.
        """
        value = self.config.get(key, default)
        logger.debug(f"Retrieved configuration key '{key}' with value: '{value}'")
        return value

# Example usage
if __name__ == "__main__":
    # Define project root path dynamically
    script_dir = Path(__file__).resolve().parent
    project_root = script_dir.parent.parent
    config_file_path = project_root / 'config' / 'config.yaml'

    logger.debug(f"Project root path: {project_root}")
    logger.debug(f"Configuration file path: {config_file_path}")

    # Initialize ConfigManager with configuration file
    config_manager = ConfigManager(config_file=config_file_path)
    
    # Retrieve and log configuration values
    loading_path = config_manager.get('loading_path')
    api_key = config_manager.get('api_key')
    base_url = config_manager.get('base_url')
    timeout = config_manager.get('timeout')
    db_name = config_manager.get('db_name')
    db_user = config_manager.get('db_user')

    logger.info(f"ConfigManager loaded values: "
                f"Loading Path='{loading_path}', "
                f"API Key='{api_key}', "
                f"Base URL='{base_url}', "
                f"Timeout='{timeout}', "
                f"DB Name='{db_name}', "
                f"DB User='{db_user}'")
