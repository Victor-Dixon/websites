import Foundation
import Core
import Shared

#if canImport(UserNotifications)
import UserNotifications
#endif
import Models // To access Alert

/// Delegate protocol for handling notification-related errors.
public protocol NotificationManagerDelegate: AnyObject {
    /// Called when scheduling a notification fails.
    /// - Parameters:
    ///   - alert: The alert for which scheduling failed.
    ///   - error: The error that occurred.
    func didFailToScheduleNotification(for alert: Alert, error: Error)
    
    /// Called when requesting authorization fails.
    /// - Parameter error: The error that occurred.
    func didFailToRequestAuthorization(error: Error)
}

/// Protocol defining notification management functionalities.
public protocol NotificationManagerProtocol {
    /// Requests notification authorization from the user.
    /// - Parameter completion: A closure that returns a Boolean indicating whether authorization was granted.
    func requestAuthorization(completion: @escaping (Bool) -> Void)
    
    /// Schedules a notification for a specific stock alert.
    /// - Parameters:
    ///   - alert: The stock alert to notify about.
    ///   - currentPrice: The current price of the stock.
    func scheduleNotification(for alert: Alert, currentPrice: Double)
}
