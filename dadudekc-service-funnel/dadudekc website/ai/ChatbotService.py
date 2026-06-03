from chatterbot import ChatBot
from chatterbot.trainers import ChatterBotCorpusTrainer
import logging

class ChatbotService:
    def __init__(self, name='DaDudeKC', database_uri='sqlite:///da_dude_kc_database.sqlite3', logging_level=logging.INFO):
        """Initialize the chatbot with configurable database URI and detailed logging."""
        logging.basicConfig(level=logging_level, format='%(asctime)s - %(levelname)s - %(message)s')

        self.chatbot = ChatBot(
            name,
            storage_adapter='chatterbot.storage.SQLStorageAdapter',
            database_uri=database_uri,
            logic_adapters=[
                {
                    'import_path': 'chatterbot.logic.BestMatch',
                    'default_response': 'I am not sure how to respond to that.',
                    'maximum_similarity_threshold': 0.90
                }
            ]
        )
        logging.info("Chatbot initialized with database at: %s", database_uri)

    def train_chatbot(self, corpus_paths=None):
        """Train the chatbot using specified corpus data. Accepts a list of corpus paths."""
        if corpus_paths is None:
            corpus_paths = ['chatterbot.corpus.english']

        trainer = ChatterBotCorpusTrainer(self.chatbot)
        for corpus in corpus_paths:
            try:
                trainer.train(corpus)
                logging.info("Chatbot trained successfully using the %s corpus.", corpus)
            except Exception as e:
                logging.error("Failed to train chatbot on %s: %s", corpus, str(e))

    def get_response(self, message):
        """Retrieve a response from the chatbot based on user input, with error handling."""
        try:
            response = self.chatbot.get_response(message)
            return response.text
        except Exception as e:
            logging.error("Failed to get response: %s", str(e))
            return "Sorry, I encountered an issue."

# Sample usage
if __name__ == "__main__":
    chatbot_service = ChatbotService()
    chatbot_service.train_chatbot(['chatterbot.corpus.english', 'chatterbot.corpus.spanish'])

    # Example interaction
    user_input = "Hello, how are you?"
    response = chatbot_service.get_response(user_input)
    print(response)
