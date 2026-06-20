import Foundation
import Models

// Define the structure for the Alert
struct Alert {
    var stockSymbol: String
    var alertType: AlertType
    var conditionValue: Double
    var isActive: Bool
}

enum AlertType {
    case priceAbove, priceBelow

    var displayName: String {
        switch self {
        case .priceAbove: return "Price Above"
        case .priceBelow: return "Price Below"
        }
    }
}

class AlertsViewModel {
    // Store and manage alerts
    var alerts: [Alert] = []

    func addAlert(_ alert: Alert) {
        alerts.append(alert)
    }

    func updateAlertStatus(alert: Alert, isActive: Bool) {
        if let index = alerts.firstIndex(where: { $0.stockSymbol == alert.stockSymbol }) {
            alerts[index].isActive = isActive
        }
    }

    func deleteAlerts(at offsets: IndexSet) {
        alerts.remove(atOffsets: offsets)
    }
}

// Command-line implementation
func displayAlerts(viewModel: AlertsViewModel) {
    print("Alerts:")
    for alert in viewModel.alerts {
        print("Symbol: \(alert.stockSymbol), Type: \(alert.alertType.displayName), Condition: \(alert.conditionValue), Active: \(alert.isActive)")
    }
}

func main() {
    let viewModel = AlertsViewModel()

    // Simulating adding alerts
    viewModel.addAlert(Alert(stockSymbol: "AAPL", alertType: .priceAbove, conditionValue: 150.0, isActive: true))
    viewModel.addAlert(Alert(stockSymbol: "AAPL", alertType: .priceBelow, conditionValue: 130.0, isActive: false))

    // Display alerts in the console
    displayAlerts(viewModel: viewModel)

    // Example of updating alert status
    if let firstAlert = viewModel.alerts.first {
        viewModel.updateAlertStatus(alert: firstAlert, isActive: false)
    }

    // Display alerts after update
    print("\nUpdated Alerts:")
    displayAlerts(viewModel: viewModel)
}
