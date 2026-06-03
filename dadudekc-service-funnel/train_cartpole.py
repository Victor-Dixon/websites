#!/usr/bin/env python3
"""
CartPole Training Script
========================
Standalone script for training CartPole with Q-learning
"""

import gym
import random
import numpy as np
from collections import deque
import torch
import torch.nn as nn
import torch.optim as optim
import warnings
import argparse
from pathlib import Path

# Import our Q-learning components
import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), 'AI agent'))
from main import QNetwork, train_batch, save_model, load_model, choose_action

warnings.filterwarnings("ignore", category=DeprecationWarning)

def train_cartpole(episodes=1000, gamma=0.99, epsilon_start=1.0, 
                   epsilon_min=0.01, epsilon_decay=0.995, batch_size=20, 
                   memory_size=2000, learning_rate=0.001, save_interval=100):
    """
    Train CartPole with Q-learning
    
    Args:
        episodes: Number of training episodes
        gamma: Discount factor
        epsilon_start: Starting exploration rate
        epsilon_min: Minimum exploration rate
        epsilon_decay: Exploration rate decay
        batch_size: Batch size for training
        memory_size: Maximum memory size
        learning_rate: Learning rate for optimizer
        save_interval: How often to save model (episodes)
    """
    
    # Initialize environment
    env = gym.make('CartPole-v1')
    state_size = env.observation_space.shape[0]
    action_size = env.action_space.n
    
    print(f"Training CartPole with state size: {state_size}, action size: {action_size}")
    
    # Initialize network and optimizer
    q_network = QNetwork(state_size, action_size)
    optimizer = optim.Adam(q_network.parameters(), lr=learning_rate)
    
    # Training variables
    epsilon = epsilon_start
    memory = deque(maxlen=memory_size)
    
    # Training loop
    best_score = 0
    scores = []
    
    for episode in range(episodes):
        state, _ = env.reset()
        state = np.reshape(state, [1, state_size])
        total_reward = 0
        
        for timestep in range(500):  # 500 timesteps max
            action = choose_action(state, epsilon, env)
            next_state, reward, done, _, _ = env.step(action)
            next_state = np.reshape(next_state, [1, state_size])
            
            # Store the transition in memory
            memory.append((state, action, reward, next_state, done))
            
            # Move to the next state
            state = next_state
            total_reward += reward
            
            # Perform one step of the optimization
            train_batch(q_network, optimizer, memory, batch_size, gamma)
            
            if done:
                break
        
        # Update exploration rate
        if epsilon > epsilon_min:
            epsilon *= epsilon_decay
        
        scores.append(total_reward)
        
        # Track best score
        if total_reward > best_score:
            best_score = total_reward
        
        # Print progress
        if episode % 10 == 0:
            avg_score = np.mean(scores[-10:])
            print(f"Episode: {episode+1}/{episodes}, Score: {total_reward}, "
                  f"Avg (last 10): {avg_score:.1f}, Epsilon: {epsilon:.3f}")
        
        # Save model periodically
        if episode % save_interval == 0 and episode > 0:
            save_model(q_network, f"q_network_episode_{episode}.pth")
    
    # Save the final model
    final_model_path = "q_network_final.pth"
    save_model(q_network, final_model_path)
    
    print(f"\nTraining completed!")
    print(f"Best score achieved: {best_score}")
    print(f"Final model saved to: {final_model_path}")
    
    env.close()
    return q_network, scores

def evaluate_model(model_path, episodes=10):
    """
    Evaluate a trained model
    
    Args:
        model_path: Path to the trained model
        episodes: Number of evaluation episodes
    """
    env = gym.make('CartPole-v1')
    state_size = env.observation_space.shape[0]
    action_size = env.action_space.n
    
    q_network = QNetwork(state_size, action_size)
    
    if not load_model(q_network, model_path):
        print("Failed to load model for evaluation")
        return
    
    scores = []
    
    for episode in range(episodes):
        state, _ = env.reset()
        state = np.reshape(state, [1, state_size])
        total_reward = 0
        
        for timestep in range(500):
            # Always exploit (no exploration during evaluation)
            action = choose_action(state, 0.0, env)
            next_state, reward, done, _, _ = env.step(action)
            next_state = np.reshape(next_state, [1, state_size])
            
            state = next_state
            total_reward += reward
            
            if done:
                break
        
        scores.append(total_reward)
        print(f"Evaluation Episode {episode+1}: Score = {total_reward}")
    
    avg_score = np.mean(scores)
    print(f"\nEvaluation completed!")
    print(f"Average score over {episodes} episodes: {avg_score:.1f}")
    
    env.close()
    return scores

def main():
    parser = argparse.ArgumentParser(description="Train CartPole with Q-learning")
    parser.add_argument("--mode", choices=["train", "evaluate"], default="train",
                       help="Mode: train new model or evaluate existing one")
    parser.add_argument("--model", type=str, default="q_network_final.pth",
                       help="Model file to evaluate (for evaluate mode)")
    parser.add_argument("--episodes", type=int, default=1000,
                       help="Number of training episodes")
    parser.add_argument("--eval-episodes", type=int, default=10,
                       help="Number of evaluation episodes")
    
    args = parser.parse_args()
    
    if args.mode == "train":
        print("Starting CartPole training...")
        model, scores = train_cartpole(episodes=args.episodes)
        
        # Save training history
        np.save("training_scores.npy", scores)
        print("Training scores saved to training_scores.npy")
        
    elif args.mode == "evaluate":
        print(f"Evaluating model: {args.model}")
        if not Path(args.model).exists():
            print(f"Model file {args.model} not found!")
            return
        
        scores = evaluate_model(args.model, episodes=args.eval_episodes)

if __name__ == "__main__":
    main()
