import Foundation
import Models
import Services
import Shared


class TradePlanViewModel {
    var symbol: String = ""
    var tradePlan: String = ""
    var isLoading: Bool = false
    var errorMessage: String? = nil
    
    private let serviceManager: StockServiceManager
    
    init(serviceManager: StockServiceManager) {
        self.serviceManager = serviceManager
    }
    
    // Fetch real stock data from an API (e.g., Alpha Vantage)
    func generateTradePlan() {
        guard !symbol.isEmpty else {
            self.errorMessage = "Stock symbol cannot be empty."
            return
        }
        
        isLoading = true
        serviceManager.fetchStockData(symbol: symbol) { [weak self] result in
            DispatchQueue.main.async {
                guard let self = self else { return }
                switch result {
                case .success(let stockData):
                    self.tradePlan = self.createTradePlan(for: stockData)
                    self.isLoading = false
                case .failure(let error):
                    self.errorMessage = "Failed to fetch stock data: \(error.localizedDescription)"
                    self.isLoading = false
                }
            }
        }
    }
    
    // Create a trade plan based on stock data
    private func createTradePlan(for stockData: StockData) -> String {
        let price = stockData.price
        let changePercent = stockData.changePercent
        
        if changePercent > 1.0 {
            return """
            Trade Plan for \(stockData.symbol):
            1. Buy 100 shares at $\(String(format: "%.2f", price)).
            2. Sell at a 5% gain if the price reaches $\(String(format: "%.2f", price * 1.05)).
            """
        } else {
            return """
            Trade Plan for \(stockData.symbol):
            1. Hold your position and monitor the stock.
            """
        }
    }
}
