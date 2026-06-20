// D:\TBOWTactics\Sources\Core\HistoricalStockData.swift

import Foundation

public struct HistoricalStockData: Codable, Equatable {
    public let date: Date
    public let open: Double
    public let close: Double
    public let high: Double
    public let low: Double
    public let volume: Int

    /// Initializes a new HistoricalStockData instance.
    ///
    /// - Parameters:
    ///   - date: The date of the stock data.
    ///   - open: The opening price of the stock.
    ///   - close: The closing price of the stock.
    ///   - high: The highest price of the stock.
    ///   - low: The lowest price of the stock.
    ///   - volume: The trading volume.
    public init(date: Date, open: Double, close: Double, high: Double, low: Double, volume: Int) {
        self.date = date
        self.open = open
        self.close = close
        self.high = high
        self.low = low
        self.volume = volume
    }

    // Equatable conformance
    public static func == (lhs: HistoricalStockData, rhs: HistoricalStockData) -> Bool {
        return lhs.date == rhs.date &&
               lhs.open == rhs.open &&
               lhs.close == rhs.close &&
               lhs.high == rhs.high &&
               lhs.low == rhs.low &&
               lhs.volume == rhs.volume
    }
}
