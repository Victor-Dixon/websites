import Foundation

// Mock API Call Function Type
typealias MockAPICall = (String) -> [String: Any]?

// Refined Integration Scenario Test
func refinedIntegrationScenario(
    mockAPICall: MockAPICall,
    mockAlerts: [[String: Any]],
    targetSymbol: String,
    expectedAlertCount: Int
) -> String {
    // Step 1: Fetch stock data
    guard let stockData = mockAPICall(targetSymbol),
          let stockPrice = stockData["price"] as? Double else {
        return "Test failed: Invalid or missing stock data for symbol \(targetSymbol)."
    }

    // Step 2: Simulate triggered alerts
    let triggeredAlerts = mockAlerts.filter { alert in
        guard let symbol = alert["symbol"] as? String,
              let condition = alert["condition"] as? String,
              let threshold = alert["threshold"] as? Double else { return false }

        if symbol == targetSymbol {
            if condition == "Price Above" && stockPrice > threshold {
                return true
            } else if condition == "Price Below" && stockPrice < threshold {
                return true
            }
        }
        return false
    }

    // Step 3: Validate alerts against expectations
    if triggeredAlerts.count != expectedAlertCount {
        return "Test failed: Expected \(expectedAlertCount) alerts, but got \(triggeredAlerts.count). Triggered Alerts: \(triggeredAlerts)"
    }

    return "Test passed: All expected alerts triggered correctly."
}
