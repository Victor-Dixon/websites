import Foundation
import Utilities // Assuming `toCurrency()` is defined here

/// Represents AI-generated stock predictions.
public struct 
StockPrediction: Codable, Identifiable {
    // MARK: - Properties
    public var id: UUID = UUID() // Unique identifier
    public let date: Date
    public let predictedPrice: Double
    public let confidence: Double // Confidence level in percentage

    // MARK: - Initialization
    /// Custom initializer with validation for price and confidence.
    /// - Parameters:
    ///   - date: The prediction date.
    ///   - predictedPrice: The predicted price of the stock.
    ///   - confidence: The confidence level of the prediction (0-100).
    /// - Throws: `PredictionError` if values are invalid.
    public init(date: Date, predictedPrice: Double, confidence: Double) throws {
        guard predictedPrice >= 0 else {
            throw PredictionError.invalidPredictedPrice
        }
        guard (0...100).contains(confidence) else {
            throw PredictionError.invalidConfidence
        }
        self.date = date
        self.predictedPrice = predictedPrice
        self.confidence = confidence
    }

    // MARK: - Description
    /// Provides a human-readable description of the prediction.
    public var description: String {
        """
        Date: \(DateFormatter.standard.string(from: date))
        Predicted Price: \(predictedPrice.toCurrency())
        Confidence: \(String(format: "%.2f", confidence))%
        """
    }

    // MARK: - Utility Methods
    /// Determines if the prediction meets a confidence threshold.
    /// - Parameter threshold: Minimum confidence level to meet.
    /// - Returns: `true` if the confidence is greater than or equal to the threshold.
    public func meetsConfidenceThreshold(_ threshold: Double) -> Bool {
        confidence >= threshold
    }

    /// Analyzes trends based on a list of predictions.
    /// - Parameter predictions: Array of `StockPrediction` instances.
    /// - Returns: A string describing the trend.
    public static func analyzeTrend(predictions: [StockPrediction]) -> String {
        guard !predictions.isEmpty else {
            return "No predictions available for trend analysis."
        }
        let sortedPredictions = predictions.sorted(by: { $0.date < $1.date })
        guard let first = sortedPredictions.first?.predictedPrice,
              let last = sortedPredictions.last?.predictedPrice else {
            return "Trend analysis failed due to incomplete data."
        }

        if last > first {
            return "Uptrend detected."
        } else if last < first {
            return "Downtrend detected."
        } else {
            return "Stable trend detected."
        }
    }

    // MARK: - Error Handling
    /// Errors for invalid prediction data.
    public enum PredictionError: LocalizedError {
        case invalidPredictedPrice
        case invalidConfidence

        public var errorDescription: String? {
            switch self {
            case .invalidPredictedPrice:
                return "Predicted price must be non-negative."
            case .invalidConfidence:
                return "Confidence must be between 0 and 100."
            }
        }
    }
}
