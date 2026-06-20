import Foundation
import Models
import Services
import Shared

/// ViewModel for handling historical stock data.
public class HistoricalDataViewModel: ObservableObject {
    @Published public var symbol: String = ""
    @Published public var historicalData: [HistoricalStockData]?
    @Published public var isLoading: Bool = false
    @Published public var errorMessage: String?
    @Published public var averagePrice: Double?
    @Published public var priceTrend: String?

    private let networkService: NetworkServiceProtocol

    /// Initializes a new `HistoricalDataViewModel`.
    /// - Parameter networkService: The network service to use for fetching data.
    public init(networkService: NetworkServiceProtocol = NetworkManager.shared) {
        self.networkService = networkService
    }

    /// Fetches historical data for the specified symbol.
    /// - Parameter completion: Completion handler called after fetching data.
    public func fetchHistoricalData(completion: @escaping () -> Void) {
        let trimmedSymbol = symbol.trimmingCharacters(in: .whitespacesAndNewlines).uppercased()
        guard !trimmedSymbol.isEmpty else {
            errorMessage = "Please enter a valid stock symbol."
            completion()
            return
        }

        isLoading = true
        networkService.fetchHistoricalData(symbol: trimmedSymbol) { [weak self] result in
            DispatchQueue.main.async {
                self?.isLoading = false
                switch result {
                case .success(let dataPoints):
                    self?.historicalData = dataPoints
                    self?.processHistoricalData()
                case .failure(let error):
                    self?.errorMessage = error.localizedDescription
                }
                completion()
            }
        }
    }

    /// Processes the fetched historical data to calculate average price and trend.
    private func processHistoricalData() {
        guard let dataPoints = historicalData else { return }

        // Calculate the average closing price
        let total = dataPoints.reduce(0.0) { $0 + $1.close }
        averagePrice = total / Double(dataPoints.count)

        // Determine the price trend
        if let first = dataPoints.first?.close, let last = dataPoints.last?.close {
            if last > first {
                priceTrend = "Uptrend"
            } else if last < first {
                priceTrend = "Downtrend"
            } else {
                priceTrend = "Stable"
            }
        }
    }
}
