// D:\TBOWTactics\Sources\Services\NetworkServiceProtocol.swift

import Foundation
import Models
import Core
import Shared

/// Protocol defining network service operations.
public protocol NetworkServiceProtocol {
    /// Fetches stock data for a given symbol.
    func fetchStockData(
        symbol: String,
        completion: @escaping (Result<StockData, NetworkError>) -> Void
    )
    
    /// Fetches historical stock data for a given symbol.
    func fetchHistoricalData(
        symbol: String,
        completion: @escaping (Result<[HistoricalStockData], NetworkError>) -> Void
    )
    
    /// Fetches stock metadata for a given symbol.
    func fetchStockMetadata(
        symbol: String,
        completion: @escaping (Result<[String: Any], NetworkError>) -> Void
    )
}
