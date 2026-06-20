// File: OpenAIServiceProtocol.swift

import Foundation

/// Protocol for services interacting with OpenAI API to generate trade plans.
@MainActor
public protocol OpenAIServiceProtocol {
    /// Generates a trade plan for a given stock symbol and stock data.
    ///
    /// - Parameters:
    ///   - symbol: The stock symbol (e.g., "AAPL").
    ///   - stockData: Data related to the stock, including price and change percentage.
    /// - Returns: A string containing the generated trade plan.
    /// - Throws: `NetworkError` if the generation fails due to networking or data issues.
    func generateTradePlan(symbol: String, stockData: StockData) async throws -> String
}
