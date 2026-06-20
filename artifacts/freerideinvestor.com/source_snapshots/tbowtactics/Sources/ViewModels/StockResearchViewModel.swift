import Foundation
import Models
import Services
import Shared

/// Delegate protocol to communicate updates and errors to the UI
public protocol StockResearchViewModelDelegate: AnyObject {
    func didFetchStockData(_ data: StockData)
    func didGenerateTradePlan(_ plan: String)
    func didEncounterError(_ error: String?)
}

@MainActor
public class StockResearchViewModel {
    // MARK: - Properties
    public var symbol: String = ""
    public var stockData: StockData?
    public var tradePlan: String = ""
    public var isLoading: Bool = false
    public var isGeneratingPlan: Bool = false
    public var errorMessage: String?
    
    private let networkService: NetworkServiceProtocol
    private let openAIService: OpenAIServiceProtocol
    
    public weak var delegate: StockResearchViewModelDelegate?
    
    // MARK: - Initialization
    public init(
        networkService: NetworkServiceProtocol = NetworkManager.shared,
        openAIService: OpenAIServiceProtocol = OpenAIService.shared
    ) {
        self.networkService = networkService
        self.openAIService = openAIService
    }
    
    // MARK: - Fetch Stock Data
    public func fetchStockData(symbol: String) async {
        self.symbol = symbol
        self.isLoading = true
        do {
            let data = try await networkService.fetchStockData(symbol: symbol)
            self.stockData = data
            self.delegate?.didFetchStockData(data)
        } catch {
            self.errorMessage = error.localizedDescription
            self.delegate?.didEncounterError(error.localizedDescription)
        }
        self.isLoading = false
    }
    
    // MARK: - Generate Trade Plan
    public func generateTradePlan() async {
        guard let data = stockData else {
            self.errorMessage = "No stock data available. Fetch stock data first."
            self.delegate?.didEncounterError(self.errorMessage)
            return
        }
        
        isGeneratingPlan = true
        do {
            let plan = try await openAIService.generateTradePlan(symbol: data.symbol, stockData: data)
            self.tradePlan = plan
            self.delegate?.didGenerateTradePlan(plan)
        } catch {
            self.errorMessage = error.localizedDescription
            self.delegate?.didEncounterError(error.localizedDescription)
        }
        isGeneratingPlan = false
    }
}
