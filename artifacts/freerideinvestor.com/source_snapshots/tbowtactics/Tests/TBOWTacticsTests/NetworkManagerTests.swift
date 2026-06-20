import XCTest

final class NetworkManagerTests: XCTestCase {
    private let networkManager = NetworkManager.shared

    func testFetchStockData() {
        let expectation = XCTestExpectation(description: "Fetch stock data for TSLA")

        networkManager.fetchStockData(symbol: "TSLA") { result in
            switch result {
            case .success(let data):
                XCTAssertEqual(data.price, 500.00)
                XCTAssertEqual(data.changePercent, 1.5)
            case .failure:
                XCTFail("Expected successful response for TSLA")
            }
            expectation.fulfill()
        }

        wait(for: [expectation], timeout: 2.0)
    }

    func testFetchHistoricalData() {
        let expectation = XCTestExpectation(description: "Fetch historical data for TSLA")

        networkManager.fetchHistoricalData(symbol: "TSLA") { result in
            switch result {
            case .success(let historicalData):
                XCTAssertEqual(historicalData.count, 5)
                XCTAssert(historicalData.first!.close > historicalData.first!.open)
            case .failure:
                XCTFail("Expected successful response for TSLA historical data")
            }
            expectation.fulfill()
        }

        wait(for: [expectation], timeout: 3.0)
    }

    func testInvalidSymbol() {
        let expectation = XCTestExpectation(description: "Validate invalid symbol")

        networkManager.fetchStockData(symbol: "1234") { result in
            switch result {
            case .success:
                XCTFail("Expected failure for invalid symbol")
            case .failure(let error):
                XCTAssertEqual(error.localizedDescription, "The stock symbol is invalid.")
            }
            expectation.fulfill()
        }

        wait(for: [expectation], timeout: 1.0)
    }
}
