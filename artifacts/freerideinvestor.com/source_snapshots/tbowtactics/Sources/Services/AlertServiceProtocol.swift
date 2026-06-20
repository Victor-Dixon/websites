// D:\TBOWTactics\Sources\Services\AlertServiceProtocol.swift

import Foundation
import Models

public protocol AlertServiceProtocol {
    var alerts: [Alert] { get set }
    
    /// Fetches all alerts.
    func fetchAlerts() throws -> [Alert]
    
    /// Adds a new alert.
    func addAlert(_ alert: Alert) throws
    
    /// Deletes specified alerts.
    func deleteAlerts(_ alertsToDelete: [Alert]) throws
    
    /// Updates an existing alert's active status.
    func updateAlert(_ alert: Alert, isActive: Bool) throws
}
