import Foundation
import Shared
import Core


class InvestmentAdvisor {
    private let confidenceThreshold: Double

    init(confidenceThreshold: Double = 90.0) {
        self.confidenceThreshold = confidenceThreshold
    }

    // Generate insights from stock predictions
    func generateInsights(from predictions: [StockPrediction]) -> [String] {
        guard !predictions.isEmpty else { return ["No predictions available for insights."] }

        var insights: [String] = []

        // Analyze trend
        let trend = StockPrediction.analyzeTrend(predictions: predictions)
        insights.append("Trend Analysis: \(trend)")

        // Filter high-confidence predictions
        let highConfidencePredictions = predictions.filter { $0.meetsConfidenceThreshold(confidenceThreshold) }
        if highConfidencePredictions.isEmpty {
            insights.append("No predictions met the confidence threshold of \(confidenceThreshold)%.")
        } else {
            insights.append("High-confidence predictions (\(confidenceThreshold)% or higher):")
            highConfidencePredictions.forEach {
                insights.append($0.description)
            }
        }

        // Highlight investment opportunities
        if let bestPrediction = highConfidencePredictions.max(by: { $0.predictedPrice < $1.predictedPrice }) {
            insights.append("Investment Opportunity: Focus on \(bestPrediction.date) with a predicted price of \(bestPrediction.predictedPrice.toCurrency()) and \(String(format: "%.2f", bestPrediction.confidence))% confidence.")
        }

        return insights
    }
}
