import Foundation
import Shared
import Core
import Utilities

public struct StockDataResponse: Codable {
    public let globalQuote: StockData

    enum CodingKeys: String, CodingKey {
        case globalQuote = "Global Quote"
    }

    public init(from decoder: Decoder) throws {
        let container = try decoder.container(keyedBy: CodingKeys.self)
        do {
            self.globalQuote = try container.decode(StockData.self, forKey: .globalQuote)
        } catch {
            // Log raw JSON if available for debugging
            if let rawData = decoder.userInfo[.originalData] as? Data {
                let rawResponse = try? JSONSerialization.jsonObject(with: rawData, options: .allowFragments)
                let debugInfo = rawResponse.map { "\($0)" } ?? "No raw JSON available."
                print("Debugging raw response: \(debugInfo)")
            }

            // Re-throw the decoding error
            throw DecodingError.dataCorrupted(
                DecodingError.Context(
                    codingPath: container.codingPath,
                    debugDescription: "Failed to decode 'globalQuote' in StockDataResponse."
                )
            )
        }
    }
}
