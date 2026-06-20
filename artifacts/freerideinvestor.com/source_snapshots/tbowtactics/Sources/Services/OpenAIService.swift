// File: D:\TBOWTactics\Sources\Services\OpenAIService.swift

import Foundation
#if canImport(FoundationNetworking)
import FoundationNetworking
#endif

import Shared // For StockData, Constants, NetworkError

/// Response structure for the OpenAI API.
public struct OpenAIResponse: Codable {
    public let choices: [Choice]

    public struct Choice: Codable {
        public let message: Message

        public struct Message: Codable {
            public let content: String
        }
    }
}

/// Manages interactions with OpenAI's API for generating trade plans.
@MainActor
public class OpenAIService: OpenAIServiceProtocol {
    // Singleton instance for global access
    public static let shared = OpenAIService()
    
    // URLSession for network requests
    private let session: URLSession
    
    // API key and endpoint from shared constants
    private let apiKey: String
    private let endpoint: String

    /// Initializes the OpenAIService with default or custom parameters.
    ///
    /// - Parameters:
    ///   - session: The URLSession instance to use for network requests. Defaults to `.shared`.
    ///   - apiKey: The OpenAI API key. Defaults to `Constants.API.openAIAPIKey`.
    public init(
        session: URLSession = .shared,
        apiKey: String = Constants.API.openAIAPIKey
    ) {
        self.session = session
        self.apiKey = apiKey
        self.endpoint = Constants.API.openAIBaseURL
    }

    /// Generates a day trading plan using OpenAI's API.
    ///
    /// - Parameters:
    ///   - symbol: The stock symbol (e.g., "AAPL").
    ///   - stockData: Data related to the stock, including price and change percentage.
    /// - Returns: A string containing the generated trade plan.
    /// - Throws: `NetworkError` if the generation fails due to networking or data issues.
    public func generateTradePlan(symbol: String, stockData: StockData) async throws -> String {
        // Validate input
        guard !symbol.isEmpty else {
            throw NetworkError.invalidSymbol
        }

        // Construct the URL
        guard let url = URL(string: endpoint) else {
            throw NetworkError.invalidURL
        }

        // Build the request with necessary headers and body
        let request = try buildRequest(
            url: url,
            prompt: generateTradePrompt(symbol: symbol, stockData: stockData)
        )

        // Perform the network request asynchronously
        let (data, response) = try await session.data(for: request)

        // Validate the HTTP response status
        guard let httpResponse = response as? HTTPURLResponse,
              (200...299).contains(httpResponse.statusCode) else {
            throw NetworkError.invalidResponse
        }

        // Decode the JSON response from OpenAI
        let openAIResponse = try JSONDecoder().decode(OpenAIResponse.self, from: data)
        
        // Extract and return the trade plan content
        return openAIResponse.choices.first?.message.content ?? "No content"
    }

    /// Builds a URLRequest for the OpenAI API.
    ///
    /// - Parameters:
    ///   - url: The API endpoint URL.
    ///   - prompt: The prompt string to send to OpenAI.
    /// - Returns: A configured URLRequest object.
    /// - Throws: An error if JSON serialization fails.
    private func buildRequest(url: URL, prompt: String) throws -> URLRequest {
        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.addValue("Bearer \(apiKey)", forHTTPHeaderField: "Authorization")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")

        // Define the request body with necessary parameters
        let body: [String: Any] = [
            "model": "gpt-3.5-turbo",
            "messages": [
                ["role": "system", "content": "You are a professional stock trader."],
                ["role": "user", "content": prompt]
            ],
            "max_tokens": 300,
            "temperature": 0.7
        ]

        // Serialize the body to JSON data
        request.httpBody = try JSONSerialization.data(withJSONObject: body)
        return request
    }

    /// Generates a prompt string based on the stock symbol and data.
    ///
    /// - Parameters:
    ///   - symbol: The stock symbol.
    ///   - stockData: Data related to the stock.
    /// - Returns: A formatted prompt string for OpenAI.
    private func generateTradePrompt(symbol: String, stockData: StockData) -> String {
        return """
        You are a professional stock trader. Given the following stock data:
        - Symbol: \(symbol)
        - Current Price: \(stockData.toCurrency())
        - Daily Change: \(stockData.changePercent)%

        Generate a concise day trading plan for \(symbol) that includes:
        1. Entry price and conditions.
        2. Exit price and profit target.
        3. Stop-loss price for risk management.
        4. Key observations or warnings based on the data.
        """
    }
}
