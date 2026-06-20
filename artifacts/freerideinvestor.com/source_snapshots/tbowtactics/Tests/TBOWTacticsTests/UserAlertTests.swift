import Foundation

// Define the structure for the Alert and StockData
struct Alert {
    let symbol: String
    let condition: String
    let threshold: Double
}

struct StockData {
    let symbol: String
    let price: Double
}

// Function to simulate refined user alert scenarios
func refinedUserAlertScenarios(mockAlerts: [Alert], mockData: StockData, targetSymbol: String) -> String {
    do {
        var triggeredAlerts: [Alert] = []
        
        for alert in mockAlerts {
            if alert.symbol == targetSymbol { // Only process alerts for the target symbol
                if alert.condition == "Price Above" && mockData.price > alert.threshold {
                    triggeredAlerts.append(alert)
                } else if alert.condition == "Price Below" && mockData.price < alert.threshold {
                    triggeredAlerts.append(alert)
                }
            }
        }
        
        // Check if the triggered alerts align with expectations
        let expectedTriggeredAlerts: [Alert] = [] // For AAPL at 150.0, no alerts should trigger
        
        // Use assert to check if the triggered alerts match the expected
        assert(triggeredAlerts == expectedTriggeredAlerts, "Expected \(expectedTriggeredAlerts), got \(triggeredAlerts)")

        return "refinedUserAlertScenarios passed"
    } catch {
        return "refinedUserAlertScenarios failed: \(error)"
    }
}
