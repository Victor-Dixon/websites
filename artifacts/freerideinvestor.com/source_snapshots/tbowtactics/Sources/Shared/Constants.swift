import Foundation

/// Application-wide constants and configurations.
public struct Constants {
    
    /// API-related constants.
    public struct API {
        public static let alphaVantageBaseURL = "https://www.alphavantage.co/query"
        public static let openAIBaseURL = "https://api.openai.com/v1/chat/completions"
        
        public static var alphaVantageAPIKey: String {
            ProcessInfo.processInfo.environment["ALPHA_VANTAGE_API_KEY"] ?? ""
        }
        
        public static var openAIAPIKey: String {
            ProcessInfo.processInfo.environment["OPENAI_API_KEY"] ?? ""
        }
    }
    
    /// Notification-related constants.
    public struct AppNotification {
        public static let tradePlanGenerated = Notification.Name("TradePlanGenerated")
    }
    
    /// Date formatter used throughout the application.
    public static let dateFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy-MM-dd"
        return formatter
    }()
}