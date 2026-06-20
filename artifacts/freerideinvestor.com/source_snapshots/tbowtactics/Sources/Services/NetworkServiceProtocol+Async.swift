// NetworkServiceProtocol+Async.swift

import Foundation
import Models
import Shared
import Core

extension NetworkServiceProtocol {
    
    /// Async wrapper for `fetchStockData(symbol:completion:)`.
    public func fetchStockData(symbol: String) async throws -> StockData {
        try await withCheckedThrowingContinuation { continuation in
            self.fetchStockData(symbol: symbol) { result in
                switch result {
                case .success(let stockData):
                    continuation.resume(returning: stockData)
                case .failure(let error):
                    continuation.resume(throwing: error)
                }
            }
        }
    }
    
    /// Async wrapper for `fetchHistoricalData(symbol:completion:)`.
    public func fetchHistoricalData(symbol: String) async throws -> [HistoricalStockData] {
        try await withCheckedThrowingContinuation { continuation in
            self.fetchHistoricalData(symbol: symbol) { result in
                switch result {
                case .success(let historicalData):
                    continuation.resume(returning: historicalData)
                case .failure(let error):
                    continuation.resume(throwing: error)
                }
            }
        }
    }
    
    /// Async wrapper for `fetchStockMetadata(symbol:completion:)`.
    public func fetchStockMetadata(symbol: String) async throws -> [String: Any] {
        try await withCheckedThrowingContinuation { continuation in
            self.fetchStockMetadata(symbol: symbol) { result in
                switch result {
                case .success(let metadata):
                    continuation.resume(returning: metadata)
                case .failure(let error):
                    continuation.resume(throwing: error)
                }
            }
        }
    }
}
