import Foundation
import Services
import Shared

class StockResearchViewModel {
    // MARK: - Properties
    var symbol: String = ""
    var stockData: StockData? = nil
    var tradePlan: String = ""
    var errorMessage: String? = nil
    var isLoading: Bool = false

    // MARK: - Fetch Stock Data
    func fetchStockData(completion: @escaping () -> Void) {
        guard !symbol.isEmpty else {
            errorMessage = "Stock symbol cannot be empty."
            completion()
            return
        }

        isLoading = true

        NetworkManager.shared.fetchStockData(symbol: symbol) { [weak self] result in
            DispatchQueue.main.async {
                self?.isLoading = false
                switch result {
                case .success(let data):
                    self?.stockData = data
                    self?.errorMessage = nil
                case .failure(let error):
                    self?.stockData = nil
                    self?.errorMessage = "Failed to fetch data: \(error.localizedDescription)"
                }
                completion()
            }
        }
    }

    // MARK: - Generate Trade Plan
    func generateTradePlan() {
        guard let data = stockData else {
            errorMessage = "No stock data available."
            return
        }

        tradePlan = """
        Trade Plan for \(data.symbol):
        - Entry price: Above \(String(format: "%.2f", data.price * 0.98))
        - Exit price: Below \(String(format: "%.2f", data.price * 1.02))
        - Stop-loss: Below \(String(format: "%.2f", data.price * 0.95))
        """
    }
}
