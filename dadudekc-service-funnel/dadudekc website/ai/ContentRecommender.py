from surprise import Dataset, Reader, KNNBasic, accuracy, dump
from surprise.model_selection import train_test_split, GridSearchCV
import pandas as pd

class ContentRecommender:
    def __init__(self, data):
        """Initialize with data, which is a list of lists containing user, item, and rating."""
        self.data = data
        self.algo = None

    def load_data(self):
        """Load data into Surprise's format from a pandas dataframe."""
        reader = Reader(rating_scale=(1, 5))
        return Dataset.load_from_df(pd.DataFrame(self.data, columns=['userID', 'itemID', 'rating']), reader)

    def train_and_evaluate(self, use_grid_search=False):
        """Train the KNN model and evaluate it using RMSE. Optionally use grid search for tuning."""
        data = self.load_data()
        trainset, testset = train_test_split(data, test_size=0.25)
        
        if use_grid_search:
            param_grid = {
                'k': [10, 20, 30], 
                'sim_options': {
                    'name': ['cosine', 'msd', 'pearson'],
                    'user_based': [False, True]
                }
            }
            gs = GridSearchCV(KNNBasic, param_grid, measures=['rmse'], cv=3)
            gs.fit(data)
            self.algo = gs.best_estimator['rmse']
            print(f"Best RMSE score obtained: {gs.best_score['rmse']}")
            print(f"Best parameters: {gs.best_params['rmse']}")
        else:
            self.algo = KNNBasic(sim_options={'name': 'cosine', 'user_based': False})
            self.algo.fit(trainset)
        
        predictions = self.algo.test(testset)
        rmse = accuracy.rmse(predictions)
        print(f"Evaluated RMSE: {rmse}")

    def save_model(self, file_path):
        """Save the trained model to a file."""
        dump.dump(file_path, algo=self.algo, verbose=True)

    def load_model(self, file_path):
        """Load a trained model from a file."""
        self.algo = dump.load(file_path)[1]
        print("Model loaded successfully.")

# Sample usage
if __name__ == "__main__":
    data = [
        ['user1', 'item1', 1],
        ['user1', 'item2', 2],
        ['user2', 'item1', 2],
        ['user2', 'item3', 3],
        ['user3', 'item2', 3],
        ['user3', 'item3', 4]
    ]

    recommender = ContentRecommender(data)
    recommender.train_and_evaluate(use_grid_search=True)
    recommender.save_model('./recommender_model')
    # Optionally load the model
    # recommender.load_model('./recommender_model')
