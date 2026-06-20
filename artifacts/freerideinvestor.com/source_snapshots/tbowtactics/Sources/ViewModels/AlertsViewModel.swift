// D:\TBOWTactics\Sources\ViewModels\AlertsViewModel.swift

import Foundation
import Models
import Services // includes AlertServiceProtocol
import Shared

/// Delegate protocol to communicate updates and errors to the UI
public protocol AlertsViewModelDelegate: AnyObject {
    func didUpdateAlerts(_ alerts: [Alert])
    func didEncounterError(_ error: String?)
}

public class AlertsViewModel {
    // MARK: - Properties
    private(set) var alerts: [Alert] = [] {
        didSet { delegate?.didUpdateAlerts(alerts) }
    }
    
    private(set) var errorMessage: String? {
        didSet { delegate?.didEncounterError(errorMessage) }
    }
    
    private let alertService: AlertServiceProtocol
    public weak var delegate: AlertsViewModelDelegate?
    
    // MARK: - Initialization
    public init(alertService: AlertServiceProtocol = AlertService.shared) {
        self.alertService = alertService
        loadAlerts()
    }
    
    // MARK: - Load Alerts
    public func loadAlerts() {
        DispatchQueue.global(qos: .background).async { [weak self] in
            guard let self = self else { return }
            do {
                let fetchedAlerts = try self.alertService.fetchAlerts()
                DispatchQueue.main.async {
                    self.alerts = fetchedAlerts
                }
            } catch {
                DispatchQueue.main.async {
                    self.errorMessage = "Failed to load alerts: \(error.localizedDescription)"
                }
            }
        }
    }
    
    // MARK: - Add Alert
    public func addAlert(stockSymbol: String, alertType: AlertType, conditionValue: Double) {
        DispatchQueue.global(qos: .background).async { [weak self] in
            guard let self = self else { return }
            let newAlert = Alert(
                stockSymbol: stockSymbol.uppercased(),
                alertType: alertType,
                conditionValue: conditionValue,
                isActive: true
            )
            do {
                try self.alertService.addAlert(newAlert)
                self.loadAlerts()
            } catch {
                DispatchQueue.main.async {
                    self.errorMessage = "Failed to add alert: \(error.localizedDescription)"
                }
            }
        }
    }
    
    // MARK: - Delete Alerts
    public func deleteAlerts(at offsets: IndexSet) {
        let alertsToDelete = offsets.map { alerts[$0] }
        DispatchQueue.global(qos: .background).async { [weak self] in
            guard let self = self else { return }
            do {
                try self.alertService.deleteAlerts(alertsToDelete)
                self.loadAlerts()
            } catch {
                DispatchQueue.main.async {
                    self.errorMessage = "Failed to delete alerts: \(error.localizedDescription)"
                }
            }
        }
    }
    
    // MARK: - Update Alert Status
    public func updateAlertStatus(alert: Alert, isActive: Bool) {
        DispatchQueue.global(qos: .background).async { [weak self] in
            guard let self = self else { return }
            do {
                try self.alertService.updateAlert(alert, isActive: isActive)
                self.loadAlerts()
            } catch {
                DispatchQueue.main.async {
                    self.errorMessage = "Failed to update alert status: \(error.localizedDescription)"
                }
            }
        }
    }
}
