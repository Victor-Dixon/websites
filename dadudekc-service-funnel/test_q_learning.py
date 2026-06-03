#!/usr/bin/env python3
"""
Test suite for Q-learning CartPole implementation
"""

import pytest
import torch
import numpy as np
from unittest.mock import Mock, patch

# Import the classes from main.py
import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), 'AI agent'))

# Mock gym environment for testing
class MockEnv:
    def __init__(self):
        self.observation_space = Mock()
        self.observation_space.shape = (4,)
        self.action_space = Mock()
        self.action_space.n = 2
        self.action_space.sample = Mock(return_value=1)
    
    def reset(self):
        return np.random.random(4)
    
    def step(self, action):
        state = np.random.random(4)
        reward = 1.0
        done = False
        info = {}
        return state, reward, done, info

class TestQNetwork:
    """Test QNetwork class functionality"""
    
    def test_q_network_initialization(self):
        """Test QNetwork can be initialized with correct dimensions"""
        from main import QNetwork
        
        state_size = 4
        action_size = 2
        hidden_size = 64
        
        network = QNetwork(state_size, action_size, hidden_size)
        
        assert network.fc1.in_features == state_size
        assert network.fc1.out_features == hidden_size
        assert network.fc2.in_features == hidden_size
        assert network.fc2.out_features == action_size
    
    def test_q_network_forward_pass(self):
        """Test QNetwork forward pass produces correct output shape"""
        from main import QNetwork
        
        network = QNetwork(4, 2)
        input_state = torch.randn(1, 4)
        
        output = network(input_state)
        
        assert output.shape == (1, 2)
        assert torch.is_tensor(output)
    
    def test_q_network_different_batch_sizes(self):
        """Test QNetwork handles different batch sizes correctly"""
        from main import QNetwork
        
        network = QNetwork(4, 2)
        
        # Test single sample
        single_input = torch.randn(1, 4)
        single_output = network(single_input)
        assert single_output.shape == (1, 2)
        
        # Test batch of samples
        batch_input = torch.randn(10, 4)
        batch_output = network(batch_input)
        assert batch_output.shape == (10, 2)

class TestTrainingFunctions:
    """Test training-related functions"""
    
    def test_train_batch_with_sufficient_memory(self):
        """Test train_batch function with sufficient memory"""
        from main import train_batch, QNetwork
        
        # Create mock data
        batch_size = 4
        memory = [
            (np.random.random(4), 0, 1.0, np.random.random(4), False),
            (np.random.random(4), 1, 1.0, np.random.random(4), False),
            (np.random.random(4), 0, 1.0, np.random.random(4), False),
            (np.random.random(4), 1, 1.0, np.random.random(4), False),
        ]
        
        network = QNetwork(4, 2)
        optimizer = torch.optim.Adam(network.parameters(), lr=0.001)
        
        # This should not raise an error
        try:
            train_batch(network, optimizer, memory, batch_size, 0.99)
            assert True  # If we get here, no error occurred
        except Exception as e:
            pytest.fail(f"train_batch raised an exception: {e}")
    
    def test_train_batch_with_insufficient_memory(self):
        """Test train_batch function with insufficient memory"""
        from main import train_batch, QNetwork
        
        # Create insufficient memory
        batch_size = 4
        memory = [
            (np.random.random(4), 0, 1.0, np.random.random(4), False),
        ]
        
        network = QNetwork(4, 2)
        optimizer = torch.optim.Adam(network.parameters(), lr=0.001)
        
        # This should not raise an error, just return early
        try:
            train_batch(network, optimizer, memory, batch_size, 0.99)
            assert True  # If we get here, no error occurred
        except Exception as e:
            pytest.fail(f"train_batch raised an exception: {e}")

class TestActionSelection:
    """Test action selection logic"""
    
    def test_choose_action_exploration(self):
        """Test action selection during exploration phase"""
        from main import choose_action
        
        # Mock environment
        env = MockEnv()
        state = np.random.random(4)
        epsilon = 1.0  # Always explore
        
        # Mock random.uniform to always return 0.5 (less than epsilon)
        with patch('random.uniform', return_value=0.5):
            action = choose_action(state, epsilon, env)
            assert action in [0, 1]
    
    def test_choose_action_exploitation(self):
        """Test action selection during exploitation phase"""
        from main import choose_action, QNetwork
        
        # Mock environment
        env = MockEnv()
        state = np.random.random(4)
        epsilon = 0.0  # Never explore
        
        # Mock random.uniform to always return 0.5 (greater than epsilon)
        with patch('random.uniform', return_value=0.5):
            action = choose_action(state, epsilon, env)
            assert action in [0, 1]

class TestModelPersistence:
    """Test model saving and loading"""
    
    def test_save_model(self, tmp_path):
        """Test model can be saved to file"""
        from main import save_model, QNetwork
        
        network = QNetwork(4, 2)
        save_path = tmp_path / "test_model.pth"
        
        try:
            save_model(network, str(save_path))
            assert save_path.exists()
            assert save_path.stat().st_size > 0
        except Exception as e:
            pytest.fail(f"save_model failed: {e}")

if __name__ == "__main__":
    pytest.main([__file__, "-v"])

