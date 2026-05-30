# File: test_open_ai_utils.py
# Location: C:\TheTradingRobotPlugWeb\Scripts\Utilities\Tests\
# Description: This test file verifies the functionality of the OpenAIUtils class in open_ai_utils.py.

import unittest
from unittest.mock import patch, MagicMock
from Scripts.Utilities.open_ai_utils import OpenAIUtils

class TestOpenAIUtils(unittest.TestCase):

    def setUp(self):
        # Initialize the OpenAIUtils instance for testing
        self.openai_utils = OpenAIUtils(model="gpt-4")

    @patch('openai.ChatCompletion.create')
    def test_suggest_new_features(self, mock_create):
        # Test suggesting new features
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "Suggested feature: moving average of the closing price."
        mock_create.return_value = mock_response

        data_columns = ['close', 'volume', 'high', 'low']
        suggestion = self.openai_utils.suggest_new_features(data_columns)
        mock_create.assert_called_once()
        self.assertIn("moving average", suggestion)

    @patch('openai.ChatCompletion.create')
    def test_suggest_hyperparameters(self, mock_create):
        # Test suggesting hyperparameters
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "Consider tuning the learning rate and the number of layers."
        mock_create.return_value = mock_response

        model_name = "LSTM"
        suggestion = self.openai_utils.suggest_hyperparameters(model_name)
        mock_create.assert_called_once()
        self.assertIn("learning rate", suggestion)

    @patch('openai.ChatCompletion.create')
    def test_generate_model_report(self, mock_create):
        # Test generating a model performance report
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "The model performs well, but consider increasing the data size."
        mock_create.return_value = mock_response

        mse, rmse, mae, r2 = 0.02, 0.14, 0.10, 0.85
        report = self.openai_utils.generate_model_report(mse, rmse, mae, r2)
        mock_create.assert_called_once()
        self.assertIn("performs well", report)

    @patch('openai.ChatCompletion.create')
    def test_suggest_anomaly_detection_methods(self, mock_create):
        # Test suggesting anomaly detection methods
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "Use Isolation Forest or LOF for anomaly detection."
        mock_create.return_value = mock_response

        suggestion = self.openai_utils.suggest_anomaly_detection_methods()
        mock_create.assert_called_once()
        self.assertIn("Isolation Forest", suggestion)

    @patch('openai.ChatCompletion.create')
    def test_suggest_trading_strategy(self, mock_create):
        # Test suggesting a trading strategy
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "Consider a mean reversion strategy based on Bollinger Bands."
        mock_create.return_value = mock_response

        suggestion = self.openai_utils.suggest_trading_strategy()
        mock_create.assert_called_once()
        self.assertIn("mean reversion", suggestion)

    @patch('openai.ChatCompletion.create')
    def test_risk_management_suggestions(self, mock_create):
        # Test providing risk management suggestions
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "Implement stop-loss orders and diversify the portfolio."
        mock_create.return_value = mock_response

        suggestion = self.openai_utils.risk_management_suggestions()
        mock_create.assert_called_once()
        self.assertIn("stop-loss", suggestion)

    @patch('openai.ChatCompletion.create')
    def test_chat_with_user(self, mock_create):
        # Test interacting with the user via a chat interface
        mock_response = MagicMock()
        mock_response.choices[0].text.strip.return_value = "Sure, I can help you with trading strategies."
        mock_create.return_value = mock_response

        user_query = "Can you help me with trading strategies?"
        response = self.openai_utils.chat_with_user(user_query)
        mock_create.assert_called_once()
        self.assertIn("I can help", response)

if __name__ == '__main__':
    unittest.main()
