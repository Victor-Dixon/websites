// D:\TBOWTactics\Sources\Services\NetworkManager.swift

import Foundation
#if canImport(FoundationNetworking)
import FoundationNetworking
#endif
import Shared
import Models
import Core
import Utilities

/// Represents cached stock data to optimize network requests.
public class CachedStockData: NSObject {
    public let stockData: StockData

    public init(stockData: StockData) {
        self.stockData = stockData
    }

    public func toStockData() -> StockData {
        return stockData
    }
}

/// Manages network-related tasks like fetching stock data and historical data.
public class NetworkManager: NetworkServiceProtocol {
    public static let shared = NetworkManager()
    private let session: URLSession
    private let cache = NSCache<NSString, CachedStockData>()
    private let jsonDecoder = JSONDecoder()

    public init(configuration: URLSessionConfiguration = .default) {
        self.session = URLSession(configuration: configuration)
    }

    // MARK: - Fetch Stock Data
    public func fetchStockData(symbol: String, completion: @escaping (Result<StockData, NetworkError>) -> Void) {
        do {
            let url = try constructURL(for: "GLOBAL_QUOTE", symbol: symbol)

            // Check cache
            if let cachedData = cache.object(forKey: symbol as NSString) {
                completion(.success(cachedData.toStockData()))
                return
            }

            session.dataTask(with: url) { [weak self] data, response, error in
                guard let self = self else {
                    completion(.failure(.configurationError("Self is nil")))
                    return
                }

                if let error = error {
                    completion(.failure(.networkFailure(error.localizedDescription)))
                    return
                }

                guard let data = data else {
                    completion(.failure(.noData))
                    return
                }

                guard let httpResponse = response as? HTTPURLResponse,
                      200..<300 ~= httpResponse.statusCode else {
                    completion(.failure(.invalidResponse))
                    return
                }

                do {
                    let decodedResponse = try self.jsonDecoder.decode(StockDataResponse.self, from: data)
                    let stockData = decodedResponse.globalQuote
                    self.cache.setObject(CachedStockData(stockData: stockData), forKey: symbol as NSString)
                    completion(.success(stockData))
                } catch {
                    completion(.failure(.decodingError))
                }
            }.resume()
        } catch {
            completion(.failure(.configurationError(error.localizedDescription)))
        }
    }

    // MARK: - Fetch Historical Data
    public func fetchHistoricalData(symbol: String, completion: @escaping (Result<[HistoricalStockData], NetworkError>) -> Void) {
        do {
            let url = try constructURL(for: "TIME_SERIES_DAILY", symbol: symbol)

            session.dataTask(with: url) { [weak self] data, response, error in
                guard let self = self else {
                    completion(.failure(.configurationError("Self is nil")))
                    return
                }

                if let error = error {
                    completion(.failure(.networkFailure(error.localizedDescription)))
                    return
                }

                guard let data = data else {
                    completion(.failure(.noData))
                    return
                }

                guard let httpResponse = response as? HTTPURLResponse,
                      200..<300 ~= httpResponse.statusCode else {
                    completion(.failure(.invalidResponse))
                    return
                }

                do {
                    let decodedResponse = try self.jsonDecoder.decode(TimeSeriesDailyResponse.self, from: data)
                    let historicalData = self.parseHistoricalData(from: decodedResponse)
                    completion(.success(historicalData))
                } catch {
                    completion(.failure(.decodingError))
                }
            }.resume()
        } catch let error as NetworkError {
            completion(.failure(error))
        } catch {
            completion(.failure(.configurationError(error.localizedDescription)))
        }
    }

    // MARK: - Fetch Stock Metadata
    public func fetchStockMetadata(symbol: String, completion: @escaping (Result<[String: Any], NetworkError>) -> Void) {
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
    private func constructURL(for function: String, symbol: String) throws -> URL {
        guard !Constants.API.alphaVantageAPIKey.isEmpty else {
            throw NetworkError.configurationError("Missing Alpha Vantage API Key. Please configure the API key in your environment variables.")
        }

        let urlString = "\(Constants.API.alphaVantageBaseURL)?function=\(function)&symbol=\(symbol)&apikey=\(Constants.API.alphaVantageAPIKey)&outputsize=full"
        guard let url = URL(string: urlString) else {
            throw NetworkError.invalidURL
        }
        return url
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
