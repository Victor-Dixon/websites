import gym
import random
import numpy as np
from collections import deque
import torch
import torch.nn as nn
import torch.optim as optim
import warnings
warnings.filterwarnings("ignore", category=DeprecationWarning)

# Define the Q-network
class QNetwork(nn.Module):
    def __init__(self, state_size, action_size, hidden_size=64):
        super(QNetwork, self).__init__()
        self.fc1 = nn.Linear(state_size, hidden_size)
        self.relu = nn.ReLU()
        self.fc2 = nn.Linear(hidden_size, action_size)
    
    def forward(self, state):
        x = self.fc1(state)
        x = self.relu(x)
        return self.fc2(x)

def train_batch(q_network, optimizer, memory, batch_size, gamma):
    if len(memory) < batch_size:
        return
    batch = random.sample(memory, batch_size)
    states, actions, rewards, next_states, dones = zip(*batch)

    # Convert states to proper tensor format - states are stored as [[x,y,z,w]] so we need to squeeze
    states = torch.FloatTensor(states).squeeze(1)  # Remove extra dimension
    actions = torch.LongTensor(actions)
    rewards = torch.FloatTensor(rewards)
    next_states = torch.FloatTensor(next_states).squeeze(1)  # Remove extra dimension
    dones = torch.FloatTensor(dones)

    q_values = q_network(states).gather(1, actions.unsqueeze(1)).squeeze(1)
    next_q_values = q_network(next_states).max(1)[0]
    expected_q_values = rewards + gamma * next_q_values * (1 - dones)
    
    loss = nn.MSELoss()(q_values, expected_q_values.detach())
    optimizer.zero_grad()
    loss.backward()
    optimizer.step()

def save_model(q_network, file_name="q_network.pth"):
    torch.save(q_network.state_dict(), file_name)

# Initialize environment, network, and optimizer
env = gym.make('CartPole-v1')
state_size = env.observation_space.shape[0]
action_size = env.action_space.n
q_network = QNetwork(state_size, action_size)
optimizer = optim.Adam(q_network.parameters(), lr=0.001)

# Training hyperparameters
episodes = 1000
gamma = 0.99  # Discount rate
epsilon = 1.0  # Exploration rate
epsilon_min = 0.01
epsilon_decay = 0.995
batch_size = 20
memory = deque(maxlen=2000)

# Function to choose an action
def choose_action(state, epsilon, environment):
    if random.uniform(0, 1) < epsilon:
        return environment.action_space.sample()  # Explore
    else:
        state = torch.FloatTensor(state).unsqueeze(0)
        with torch.no_grad():
            action_values = q_network(state)
        return np.argmax(action_values.numpy())  # Exploit

def load_model(q_network, file_name="q_network.pth"):
    """Load a trained model from file"""
    try:
        q_network.load_state_dict(torch.load(file_name))
        q_network.eval()  # Set to evaluation mode
        print(f"Model loaded successfully from {file_name}")
        return True
    except FileNotFoundError:
        print(f"Model file {file_name} not found")
        return False
    except Exception as e:
        print(f"Error loading model: {e}")
        return False

# Only run training if this file is run directly (not imported)
if __name__ == "__main__":
    # Training loop
    for e in range(episodes):
        state, _ = env.reset()
        state = np.reshape(state, [1, state_size])
        total_reward = 0

        for time in range(500):  # 500 timesteps max
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
        
        if epsilon > epsilon_min:
            epsilon *= epsilon_decay

        print(f"Episode: {e+1}/{episodes}, Score: {total_reward}")

        # Optionally save the model every N episodes
        if e % 100 == 0:
            save_model(q_network, f"q_network_{e}.pth")

    # Save the final model
    save_model(q_network, "q_network_final.pth")
