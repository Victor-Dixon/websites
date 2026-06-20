import Foundation

/// Represents the response structure for daily time series data.
public struct TimeSeriesDailyResponse: Codable {
    public let timeSeries: [String: [String: String]]

    /// Coding keys to map JSON fields to struct properties.
    enum CodingKeys: String, CodingKey {
        case timeSeries = "Time Series (Daily)"
    }

    /// Custom initializer for decoding.
    public init(from decoder: Decoder) throws {
        let container = try decoder.container(keyedBy: CodingKeys.self)

        do {
            self.timeSeries = try container.decode([String: [String: String]].self, forKey: .timeSeries)
        } catch {
            throw DecodingError.dataCorrupted(
                DecodingError.Context(
                    codingPath: container.codingPath,
                    debugDescription: "Corrupted data in 'Time Series (Daily)'."
                )
            )
        }
    }
}
