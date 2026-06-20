// D:\TBOWTactics\Sources\Services\StockServiceManager.swift

import Foundation
import Models    // For StockDataResponse
import Core      // For HistoricalStockData
import Shared    // For NetworkError
#if canImport(FoundationNetworking)
import FoundationNetworking
#endif

public class StockServiceManager: NetworkServiceProtocol {
    private let session: URLSession
    private let baseURL = "https://www.alphavantage.co/query"
    private let apiKey: String

    public init(apiKey: String, session: URLSession = .shared) {
        self.apiKey = apiKey
        self.session = session
    }

    /// Fetches stock data for a given symbol.
    public func fetchStockData(symbol: String, completion: @escaping (Result<StockData, NetworkError>) -> Void) {
        guard !symbol.isEmpty else {
            completion(.failure(.invalidSymbol))
            return
        }

        // Construct the URL for the API request
        guard let url = constructURL(for: "GLOBAL_QUOTE", symbol: symbol) else {
            completion(.failure(.invalidURL))
            return
        }

        // Make the network request
        session.dataTask(with: url) { data, response, error in
            if let error = error {
                completion(.failure(.networkFailure(error.localizedDescription)))
                return
            }

            guard let httpResponse = response as? HTTPURLResponse,
                  200..<300 ~= httpResponse.statusCode else {
                completion(.failure(.invalidResponse))
                return
            }

            guard let data = data else {
                completion(.failure(.noData))
                return
            }

            do {
                let decodedResponse = try JSONDecoder().decode(StockDataResponse.self, from: data)
                let stockData = decodedResponse.globalQuote

                // Directly return StockData instead of transforming to [String: Double]
                completion(.success(stockData))
            } catch {
                completion(.failure(.decodingError))
            }
        }.resume()
    }

    /// Fetches historical stock data for a given symbol.
    public func fetchHistoricalData(symbol: String, completion: @escaping (Result<[HistoricalStockData], NetworkError>) -> Void) {
        guard !symbol.isEmpty else {
            completion(.failure(.invalidSymbol))
            return
        }

        // Construct the URL for the API request
        guard let url = constructURL(for: "TIME_SERIES_DAILY", symbol: symbol) else {
            completion(.failure(.invalidURL))
            return
        }

        // Make the network request
        session.dataTask(with: url) { data, response, error in
            if let error = error {
                completion(.failure(.networkFailure(error.localizedDescription)))
                return
            }

            guard let httpResponse = response as? HTTPURLResponse,
                  200..<300 ~= httpResponse.statusCode else {
                completion(.failure(.invalidResponse))
                return
            }

            guard let data = data else {
                completion(.failure(.noData))
                return
            }

            do {
                let decodedResponse = try JSONDecoder().decode(TimeSeriesDailyResponse.self, from: data)
                let historicalData = self.parseHistoricalData(from: decodedResponse)
                completion(.success(historicalData))
            } catch {
                completion(.failure(.decodingError))
            }
        }.resume()
    }

    /// Fetches stock metadata for a given symbol.
    public func fetchStockMetadata(symbol: String, completion: @escaping (Result<[String: Any], NetworkError>) -> Void) {
        // Implement metadata fetching logic here.
        // For example, you might call a different API endpoint.
        // For now, we'll return a mock response.

        let delay = ProcessInfo.processInfo.environment["ARTIFICIAL_DELAY"].flatMap(Double.init) ?? 0.0
        DispatchQueue.global().asyncAfter(deadline: .now() + delay) {
            let mockMetadata: [String: [String: Any]] = [
                "TSLA": ["Company": "Tesla Inc.", "Sector": "Automotive"],
                "AAPL": ["Company": "Apple Inc.", "Sector": "Technology"]
            ]
            if let metadata = mockMetadata[symbol] {
                completion(.success(metadata))
            } else {
                completion(.failure(.noData))
            }
        }
    }

    /// Constructs the URL for API requests.
    private func constructURL(for function: String, symbol: String) -> URL? {
        var components = URLComponents(string: baseURL)
        components?.queryItems = [
            URLQueryItem(name: "function", value: function),
            URLQueryItem(name: "symbol", value: symbol),
            URLQueryItem(name: "apikey", value: apiKey)
        ]
        return components?.url
    }

    /// Parses historical stock data from the API response.
    private func parseHistoricalData(from response: TimeSeriesDailyResponse) -> [HistoricalStockData] {
        return response.timeSeries.compactMap { (dateString: String, dailyData: [String: String]) -> HistoricalStockData? in
            guard
                let date = Constants.dateFormatter.date(from: dateString),
                let openString = dailyData["1. open"],
                let open = Double(openString),
                let highString = dailyData["2. high"],
                let high = Double(highString),
                let lowString = dailyData["3. low"],
                let low = Double(lowString),
                let closeString = dailyData["4. close"],
                let close = Double(closeString),
                let volumeString = dailyData["5. volume"],
                let volume = Int(volumeString)
            else {
                return nil
            }
            return HistoricalStockData(date: date, open: open, close: close, high: high, low: low, volume: volume)
        }
    }
}
