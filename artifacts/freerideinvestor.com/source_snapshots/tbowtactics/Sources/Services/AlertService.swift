// D:\TBOWTactics\Sources\Services\AlertService.swift

import Foundation
import Models
import Shared
import Core

/// Errors specific to AlertService
public enum AlertServiceError: Error, LocalizedError {
    case alertNotFound
    case encodingFailed
    case decodingFailed
    
    public var errorDescription: String? {
        switch self {
        case .alertNotFound:
            return "The alert was not found."
        case .encodingFailed:
            return "Failed to encode alerts."
        case .decodingFailed:
            return "Failed to decode alerts."
        }
    }
}

/// Service managing alerts and handling their evaluations.
public class AlertService: AlertServiceProtocol {
    public static let shared: AlertService = AlertService()
    
    public var alerts: [Alert] = []
    private let userDefaultsKey = "TBOWTacticsAlerts"
    
    // Services needed
    private let notificationManager = NotificationManager.shared
    private let networkManager = NetworkManager.shared
    
    private init() {
        do {
            try loadAlerts()
        } catch {
            print("Failed to load alerts: \(error.localizedDescription)")
            self.alerts = []
        }
    }
    
    /// Fetches all alerts.
    public func fetchAlerts() throws -> [Alert] {
        return alerts
    }
    
    /// Adds a new alert and evaluates it.
    public func addAlert(_ alert: Alert) throws {
        alerts.append(alert)
        evaluateAlert(alert)
        try saveAlerts()
    }
    
    /// Deletes specified alerts.
    public func deleteAlerts(_ alertsToDelete: [Alert]) throws {
        alerts.removeAll { alert in
            alertsToDelete.contains(where: { $0.id == alert.id })
        }
        try saveAlerts()
    }
    
    /// Updates an existing alert's active status.
    public func updateAlert(_ alert: Alert, isActive: Bool) throws {
        guard let index = alerts.firstIndex(where: { $0.id == alert.id }) else {
            throw AlertServiceError.alertNotFound
        }
        alerts[index].isActive = isActive
        if isActive {
            evaluateAlert(alerts[index])
        }
        alerts[index].updatedAt = Date()
        try saveAlerts()
    }
    
    /// Loads alerts from persistent storage.
    private func loadAlerts() throws {
        guard let data = UserDefaults.standard.data(forKey: userDefaultsKey) else {
            self.alerts = []
            return
        }
        do {
            let decoded = try JSONDecoder().decode([Alert].self, from: data)
            self.alerts = decoded
        } catch {
            throw AlertServiceError.decodingFailed
        }
    }
    
    /// Saves alerts to persistent storage.
    private func saveAlerts() throws {
        do {
            let data = try JSONEncoder().encode(alerts)
            UserDefaults.standard.set(data, forKey: userDefaultsKey)
        } catch {
            throw AlertServiceError.encodingFailed
        }
    }
    
    /// Evaluates an alert by fetching current stock data.
    private func evaluateAlert(_ alert: Alert) {
        networkManager.fetchStockData(symbol: alert.stockSymbol) { [weak self] (result: Result<StockData, NetworkError>) in
            switch result {
            case .success(let stockData):
                self?.handleAlertEvaluation(alert: alert, stockData: stockData)
            case .failure(let error):
                print("Failed to fetch stock data for alert: \(error.localizedDescription)")
            }
        }
    }
    
    /// Handles the evaluation of an alert based on current stock data.
    private func handleAlertEvaluation(alert: Alert, stockData: StockData) {
        guard alert.isActive else { return }
        switch alert.alertType {
        case .priceAbove:
            if stockData.price > alert.conditionValue {
                notificationManager.scheduleNotification(for: alert, currentPrice: stockData.price)
            }
        case .priceBelow:
            if stockData.price < alert.conditionValue {
                notificationManager.scheduleNotification(for: alert, currentPrice: stockData.price)
            }
        case .percentageChange:
            // Implement logic for percentage change
            print("Handle percentage change logic.")
        case .volumeThreshold:
            // Implement logic for volume threshold
            print("Handle volume threshold logic.")
        }
    }
}
