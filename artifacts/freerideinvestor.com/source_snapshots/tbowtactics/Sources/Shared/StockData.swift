// D:\TBOWTactics\Sources\Shared\StockData.swift

import Foundation

/// Represents cached stock data to optimize network requests.
public class CachedStockData: NSObject {
    public let stockData: StockData

    public init(stockData: StockData) {
        self.stockData = stockData
    }

    public func toStockData() -> StockData {
        return stockData
    }
}

/// Represents stock data for trading.
public struct StockData: Codable, Equatable {
    public let symbol: String
    public let price: Double
    public let changePercent: Double

    // Coding keys to map API response fields.
    enum CodingKeys: String, CodingKey {
        case symbol = "01. symbol"
        case price = "05. price"
        case changePercent = "10. change_percent" // Adjust based on actual API response
    }

    /// Initializes a new `StockData`.
    /// - Parameters:
    ///   - symbol: The stock symbol.
    ///   - price: The current price.
    ///   - changePercent: The percentage change.
    public init(symbol: String, price: Double, changePercent: Double) {
        self.symbol = symbol
        self.price = price
        self.changePercent = changePercent
    }

    /// Formats the price into a currency string.
    /// - Parameters:
    ///   - locale: The locale for formatting.
    ///   - currencyCode: Optional currency code.
    /// - Returns: A formatted currency string.
    public func toCurrency(locale: Locale = .current, currencyCode: String? = nil) -> String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.locale = locale
        if let currencyCode = currencyCode {
            formatter.currencyCode = currencyCode
        }
        return formatter.string(from: NSNumber(value: self.price)) ?? "\(self.price)"
    }

    /// Converts the `StockData` instance to a dictionary with all fields.
    /// - Returns: A dictionary representation of the stock data.
    public func toDictionary() -> [String: Any] {
        return [
            "symbol": self.symbol,
            "price": self.price,
            "changePercent": self.changePercent
        ]
    }

    /// Converts the `StockData` instance to a dictionary containing only `Double` values.
    /// - Returns: A dictionary representation with `Double` values.
    public func toDoubleDictionary() -> [String: Double] {
        return [
            "price": self.price,
            "changePercent": self.changePercent
        ]
    }

    /// Equatable conformance
    public static func == (lhs: StockData, rhs: StockData) -> Bool {
        return lhs.symbol == rhs.symbol &&
               lhs.price == rhs.price &&
               lhs.changePercent == rhs.changePercent
    }
}
