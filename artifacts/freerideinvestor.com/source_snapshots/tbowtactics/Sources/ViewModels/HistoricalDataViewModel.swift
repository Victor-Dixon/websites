import Foundation
import Models
import Services
import Core
import Shared

/// Delegate to notify changes manually (replacing @Published).
public protocol HistoricalDataViewModelDelegate: AnyObject {
    func didUpdateHistoricalData()
    func didEncounterError(_ message: String)
}

/// A cross-platform ViewModel for handling historical stock data (no Combine).
public class HistoricalDataViewModel {
    public var symbol: String = ""
    public private(set) var historicalData: [HistoricalStockData]?
    public private(set) var isLoading: Bool = false
    public private(set) var errorMessage: String?
    public private(set) var averagePrice: Double?
    public private(set) var priceTrend: String?

    private let networkService: NetworkServiceProtocol
    public weak var delegate: HistoricalDataViewModelDelegate?

    /// Initializes a new `HistoricalDataViewModel`.
    /// - Parameter networkService: The network service to use for fetching data.
    public init(networkService: NetworkServiceProtocol) {
        self.networkService = networkService
    }

    /// Fetches historical data for the specified symbol.
    /// - Parameter completion: Optional completion handler called after fetching data.
    public func fetchHistoricalData(completion: (() -> Void)? = nil) {
        let trimmedSymbol = symbol.trimmingCharacters(in: .whitespacesAndNewlines).uppercased()
        guard !trimmedSymbol.isEmpty else {
            self.errorMessage = "Please enter a valid stock symbol."
            self.delegate?.didEncounterError(self.errorMessage ?? "Invalid symbol")
            completion?()
            return
        }

        self.isLoading = true
        networkService.fetchHistoricalData(symbol: trimmedSymbol) { [weak self] result in
            guard let self = self else { return }
            self.isLoading = false

            switch result {
            case .success(let dataPoints):
                self.historicalData = dataPoints
                self.processHistoricalData()
                self.delegate?.didUpdateHistoricalData()

            case .failure(let error):
                self.errorMessage = error.localizedDescription
                self.delegate?.didEncounterError(self.errorMessage ?? "Unknown error")
            }

            completion?()
        }
    }

    /// Processes the fetched historical data to calculate average price and trend.
    private func processHistoricalData() {
        guard let dataPoints = self.historicalData, !dataPoints.isEmpty else { return }

        // Calculate the average closing price
        let total = dataPoints.reduce(0.0) { $0 + $1.close }
        self.averagePrice = total / Double(dataPoints.count)

        // Determine the price trend
        if let first = dataPoints.first?.close, let last = dataPoints.last?.close {
            if last > first {
                self.priceTrend = "Uptrend"
            } else if last < first {
                self.priceTrend = "Downtrend"
            } else {
                self.priceTrend = "Stable"
            }
        }
    }
}
