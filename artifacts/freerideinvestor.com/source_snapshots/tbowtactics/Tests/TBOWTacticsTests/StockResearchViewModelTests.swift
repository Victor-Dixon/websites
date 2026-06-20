import Foundation

// Mock Services
class MockNetworkService: NetworkServiceProtocol {
    var fetchStockDataResult: Result<StockData, NetworkError>?
    
    func fetchStockData(symbol: String, completion: @escaping (Result<StockData, NetworkError>) -> Void) {
        if let result = fetchStockDataResult {
            completion(result)
        }
    }
}

class MockOpenAIService: OpenAIServiceProtocol {
    var generateTradePlanResult: Result<String, NetworkError>?
    
    func generateTradePlan(symbol: String, stockData: StockData, completion: @escaping (Result<String, NetworkError>) -> Void) {
        if let result = generateTradePlanResult {
            completion(result)
        }
    }
}

// Custom Test Runner
func runTests() {
    let mockNetworkService = MockNetworkService()
    let mockOpenAIService = MockOpenAIService()
    let viewModel = StockResearchViewModel(
        networkService: mockNetworkService,
        openAIService: mockOpenAIService
    )
    
    // Test 1: Fetch Stock Data - Success
    print("Running Test: Fetch Stock Data - Success")
    mockNetworkService.fetchStockDataResult = .success(StockData(symbol: "AAPL", price: 150.0, changePercent: 1.5))
    viewModel.symbol = "AAPL"
    viewModel.fetchStockData()
    if let data = viewModel.stockData {
        assert(data.symbol == "AAPL", "Stock symbol mismatch")
        assert(data.price == 150.0, "Stock price mismatch")
        assert(data.changePercent == 1.5, "Stock change percent mismatch")
        print("Test Passed!")
    } else {
        print("Test Failed: Stock data not set.")
    }

    // Test 2: Fetch Stock Data - Failure
    print("Running Test: Fetch Stock Data - Failure")
    mockNetworkService.fetchStockDataResult = .failure(.noData)
    viewModel.symbol = "INVALID"
    viewModel.fetchStockData()
    if viewModel.errorMessage == NetworkError.noData.localizedDescription {
        print("Test Passed!")
    } else {
        print("Test Failed: Error message mismatch.")
    }
    
    // Test 3: Generate Trade Plan - Success
    print("Running Test: Generate Trade Plan - Success")
    let expectedPlan = "Mock Trade Plan for AAPL"
    viewModel.stockData = StockData(symbol: "AAPL", price: 150.0, changePercent: 1.5)
    mockOpenAIService.generateTradePlanResult = .success(expectedPlan)
    viewModel.generateTradePlan()
    if viewModel.tradePlan == expectedPlan {
        print("Test Passed!")
    } else {
        print("Test Failed: Trade plan mismatch.")
    }
}
