import Foundation


extension CodingUserInfoKey {
    /// Custom key to pass original raw JSON data for debugging.
    public static let originalData = CodingUserInfoKey(rawValue: "originalData")!
}

// MARK: - DateFormatter Extension
extension DateFormatter {
    /// Standard date format: `yyyy-MM-dd` with GMT timezone.
    public static let standard: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy-MM-dd"
        formatter.timeZone = TimeZone(secondsFromGMT: 0)
        return formatter
    }()

    /// Full date format: e.g., `Monday, January 1, 2024`
    public static let full: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateStyle = .full
        formatter.timeStyle = .none
        return formatter
    }()

    /// Time-only format: `HH:mm:ss`
    public static let timeOnly: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateFormat = "HH:mm:ss"
        return formatter
    }()
}

// MARK: - Double toCurrency Extension
extension Double {
    /// Converts a number to a currency-formatted string.
    /// - Parameters:
    ///   - locale: The `Locale` to use for formatting (default is `.current`).
    ///   - currencyCode: An optional currency code (e.g., "USD").
    /// - Returns: A formatted currency string.
    public func toCurrency(locale: Locale = .current, currencyCode: String? = nil) -> String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.locale = locale
        if let currencyCode = currencyCode {
            formatter.currencyCode = currencyCode
        }
        return formatter.string(from: NSNumber(value: self)) ?? "\(self)"
    }
}
