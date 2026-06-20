// D:\TBOWTactics\Sources\Models\Alert.swift

import Foundation

/// Represents an alert for monitoring stock conditions.
public struct Alert: Identifiable, Codable {
    public let id: UUID
    public let stockSymbol: String
    public let alertType: AlertType
    public let conditionValue: Double
    public let createdAt: Date
    public var updatedAt: Date?
    public var isActive: Bool

    public init(stockSymbol: String, alertType: AlertType, conditionValue: Double, isActive: Bool = true) {
        self.id = UUID()
        self.stockSymbol = stockSymbol
        self.alertType = alertType
        self.conditionValue = conditionValue
        self.createdAt = Date()
        self.updatedAt = nil
        self.isActive = isActive
    }
}

/// Types of alerts for stock monitoring.
public enum AlertType: String, Codable {
    case priceAbove
    case priceBelow
    case percentageChange
    case volumeThreshold

    public var displayName: String {
        switch self {
        case .priceAbove:       return "Price Above"
        case .priceBelow:       return "Price Below"
        case .percentageChange: return "Percentage Change"
        case .volumeThreshold:  return "Volume Threshold"
        }
    }
}

extension Alert {
    /// Creates a mock alert for testing purposes.
    public static func mock(
        stockSymbol: String = "AAPL",
        alertType: AlertType = .priceAbove,
        conditionValue: Double = 150.0
    ) -> Alert {
        Alert(
            stockSymbol: stockSymbol,
            alertType: alertType,
            conditionValue: conditionValue
        )
    }
}
