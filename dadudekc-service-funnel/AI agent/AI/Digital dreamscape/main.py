import openai
from transformers import pipeline, AutoModelForCausalLM, AutoTokenizer

# Initialize OpenAI API
openai.api_key = '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'

class Game:
    def __init__(self):
        self.running = True
        self.player_history = []
        self.inventory = []
        self.locations, self.npcs, self.items = self.setup_world()
        self.current_location = "Nexus Prime"

    def setup_world(self):
        locations = {
            "Nexus Prime": {
                "description": "The heart of the Digital Dreamscape, blending ancient lore with futuristic tech.",
                "connections": ["The Digital Forest", "The Echoing Caves"]
            },
            "The Digital Forest": {
                "description": "A vast, mysterious forest where trees flicker between physical and digital states.",
                "connections": ["Nexus Prime"]
            },
            "The Echoing Caves": {
                "description": "Caves that resonate with the lost codes of the Universal Code, echoing secrets.",
                "connections": ["Nexus Prime"]
            }
        }

        npcs = {
            "The Great Infinite": {
                "location": "Nexus Prime",
                "description": "A being of ancient wisdom, known for speaking in riddles."
            },
            "The Code Weaver": {
                "location": "The Digital Forest",
                "description": "A mysterious figure that manipulates the fabric of the Digital Dreamscape."
            }
        }

        items = {
            "Ancient Key": {
                "location": "The Echoing Caves",
                "description": "A key that seems to unlock something powerful."
            },
            "Data Crystal": {
                "location": "The Digital Forest",
                "description": "A crystal pulsing with pure data."
            }
        }

        return locations, npcs, items
    
    def interact_with_npc(self, npc_name_input):
        # Normalize the player's input to ensure case-insensitive matching
        npc_name_normalized = npc_name_input.lower()

        # Attempt to find an NPC with a name matching the normalized input
        matched_npc = None
        for npc_name, npc_info in self.npcs.items():
            if npc_name.lower() == npc_name_normalized:
                matched_npc = npc_info
                break

        if matched_npc is None:
            print("You see no one by that name here.")
            return

        print(f"Approaching {npc_name}, {matched_npc['description']}")

        for prompt in npc['dialogue_prompts']:
            if npc_name in ["The Great Infinite", "Aria", "Victor"]:
                # Use OpenAI for key characters
                response = self.call_openai_model(prompt)
            else:
                # Use open-source models for other NPCs
                model_name = "EleutherAI/gpt-neo-2.7B"  # Example model
                response = generate_response_with_opensource_ai(model_name, prompt)
            print(f"{npc_name} says, \"{response}\"")

    def generate_response_with_opensource_ai(model_name, prompt):
        tokenizer = AutoTokenizer.from_pretrained(model_name)
        model = AutoModelForCausalLM.from_pretrained(model_name)
        generator = pipeline('text-generation', model=model, tokenizer=tokenizer)
        response = generator(prompt, max_length=50, num_return_sequences=1)
        return response[0]['generated_text']
    
    def move_to_location(self, new_location):
        if new_location in self.locations[self.current_location]["connections"]:
            print(f"Moving to {new_location}...")
            self.current_location = new_location
            self.describe_location()
        else:
            print("You can't move in that direction.")

    def describe_location(self):
        location = self.locations[self.current_location]
        print(location["description"])
        for npc_name in self.npcs:
            if self.npcs[npc_name]["location"] == self.current_location:
                print(f"You see {npc_name} here.")
        for item_name in self.items:
            if self.items[item_name]["location"] == self.current_location:
                print(f"You see a {item_name} here.")

    # Inventory Management
    def pick_up_item(self, item_name):
        if item_name in self.items and self.items[item_name]["location"] == self.current_location:
            print(f"Picked up {item_name}.")
            self.inventory.append(item_name)
            self.items[item_name]["location"] = "Player Inventory"
        else:
            print(f"There's no {item_name} here.")

    def use_item(self, item_name):
        if item_name in self.inventory:
            # Implement item-specific actions here
            print(f"Using {item_name}...")
            # Example puzzle solution
            if self.current_location == "The Echoing Caves" and item_name == "Ancient Key":
                print("The key unlocks a hidden door revealing a path forward.")
        else:
            print(f"You don't have a {item_name}.")

    # Save/Load System (Concept)
    def save_game(self, slot="default"):
        game_state = {
            'player_history': self.player_history,
            'inventory': self.inventory,
            'current_location': self.current_location,
            # Add additional states as necessary
        }
        filename = f'game_save_{slot}.pickle'
        with open(filename, 'wb') as file:
            pickle.dump(game_state, file)
        print(f"Game saved in slot '{slot}'.")

    def load_game(self, slot="default"):
        filename = f'game_save_{slot}.pickle'
        try:
            with open(filename, 'rb') as file:
                game_state = pickle.load(file)
            self.player_history = game_state['player_history']
            self.inventory = game_state['inventory']
            self.current_location = game_state['current_location']
            # Load additional states as necessary
            print(f"Game loaded from slot '{slot}'.")
        except (FileNotFoundError, EOFError, pickle.UnpicklingError):
            print(f"Failed to load game from slot '{slot}'. Starting a new game.")

    def auto_save(self):
        self.save_game("auto")
        print("Game auto-saved.")


    # Simplified game loop and command processing for movement and interaction
    def display_help(self):
        print("Available commands:")
        print("  - 'look' or 'where am I': Describe your current location.")
        print("  - 'talk to [NPC name]': Start a conversation with an NPC.")
        print("  - 'go to [location]': Move to a different location.")
        print("  - 'pick up [item]': Add an item to your inventory.")
        print("  - 'use [item]': Use an item from your inventory.")
        print("  - 'inventory': Check what items you currently have.")
        print("  - 'quit' or 'exit': Save and exit the game.")
        print("  - 'help': Show this list of commands.")

    def process_command(self, command):
        command = command.lower().strip()
        if command in ["quit", "exit"]:
            self.running = False
            print("Exiting game...")
        elif command in ["help", "commands", "commands?", "what can i do"]:
            self.display_help()
        elif command in ["look", "where am i"]:
            self.describe_location()
        elif command.startswith("talk to"):
            npc_name = command[len("talk to"):].strip()  # Extract the NPC name from the command
            self.interact_with_npc(npc_name)
        elif command.startswith("go to"):
            new_location = command[5:].strip()
            self.move_to_location(new_location)
        elif command.startswith("pick up"):
            item_name = command[7:].strip()
            self.pick_up_item(item_name)
        elif command.startswith("use"):
            item_name = command[3:].strip()
            self.use_item(item_name)
        elif command == "inventory":
            self.check_inventory()
        else:
            print("I don't understand that command. Type 'help' for a list of commands.")

    def check_inventory(self):
        if self.inventory:
            print("You have the following items:")
            for item in self.inventory:
                print(f"  - {item}")
        else:
            print("Your inventory is empty.")

    def run(self):
        print("Welcome to the Digital Dreamscape.")
        self.describe_location()
        while self.running:
            command = input("> ")
            self.process_command(command)


if __name__ == "__main__":
    game = Game()
    game.run()
