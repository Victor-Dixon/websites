import Foundation
#if canImport(UserNotifications)
import UserNotifications
#endif

import Models
import Utilities

/// Manages notifications for stock alerts.
public class NotificationManager: NSObject, NotificationManagerProtocol {
    public static let shared = NotificationManager()
    public weak var delegate: NotificationManagerDelegate?
    
    #if canImport(UserNotifications)
    private let center = UNUserNotificationCenter.current()

    public override init() {
        super.init()
        center.delegate = self
    }

    public func requestAuthorization(completion: @escaping (Bool) -> Void) {
        center.requestAuthorization(options: [.alert, .sound, .badge]) { granted, error in
            if let error = error {
                self.delegate?.didFailToRequestAuthorization(error: error)
                completion(false)
                return
            }
            completion(granted)
        }
    }

    public func scheduleNotification(for alert: Alert, currentPrice: Double) {
        let content = UNMutableNotificationContent()
        content.title = "Stock Alert for \(alert.stockSymbol)"
        content.body = """
        📈 Alert Details:
        Condition: \(alert.alertType.displayName)
        Value: \(alert.conditionValue)
        Current Price: \(currentPrice)
        """
        content.sound = .default

        let trigger = UNTimeIntervalNotificationTrigger(timeInterval: 1, repeats: false)
        let request = UNNotificationRequest(identifier: alert.id.uuidString, content: content, trigger: trigger)

        center.add(request) { error in
            if let error = error {
                self.delegate?.didFailToScheduleNotification(for: alert, error: error)
            }
        }
    }

    /// Handles notification display while the app is in the foreground.
    public func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        willPresent notification: UNNotification,
        withCompletionHandler completionHandler: @escaping (UNNotificationPresentationOptions) -> Void
    ) {
        completionHandler([.banner, .sound])
    }
    #else
    public override init() { super.init() }

    public func requestAuthorization(completion: @escaping (Bool) -> Void) {
        print("Mock: Notification authorization requested.")
        completion(true)
    }

    public func scheduleNotification(for alert: Alert, currentPrice: Double) {
        print("Mock: Notification scheduled for \(alert.stockSymbol). Current price: \(currentPrice)")
    }
    #endif
}

#if canImport(UserNotifications)
extension NotificationManager: UNUserNotificationCenterDelegate {
    // Additional delegate methods if needed
}
#endif
