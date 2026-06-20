// D:\TBOWTactics\Sources\Shared\NetworkError.swift

import Foundation

// MARK: - Network Errors
/// Unified Enum for network-related errors.
public enum NetworkError: LocalizedError {
    case invalidURL
    case invalidResponse
    case networkFailure(String)
    case noData
    case decodingError
    case configurationError(String)
    case invalidSymbol
    case serverError(String)
    case encodingError

    public var errorDescription: String? {
        switch self {
        case .invalidURL:
            return "The URL is invalid."
        case .invalidResponse:
            return "The server response is invalid."
        case .networkFailure(let message):
            return "Network failure: \(message)"
        case .noData:
            return "No data received."
        case .decodingError:
            return "Failed to decode the data."
        case .configurationError(let message):
            return "Configuration error: \(message)"
        case .invalidSymbol:
            return "Invalid stock symbol."
        case .serverError(let message):
            return "Server error: \(message)"
        case .encodingError:
            return "Failed to encode the data."
        }
    }
}

// Represents errors specific to the OpenAI service.
public struct OpenAIError: LocalizedError {
    public let message: String

    public init(message: String) {
        self.message = message
    }

    public var errorDescription: String? {
        return message
    }
}

// MARK: - Error Handling
/// Handles errors and sets a user-friendly error message.
public class ErrorHandler {
    public private(set) var errorMessage: String = ""

    public func handleError(_ error: Error) {
        if let networkError = error as? NetworkError {
            errorMessage = {
                switch networkError {
                case .invalidURL:
                    return "Invalid URL. Please verify your input."
                case .noData:
                    return "No data available. Try again later."
                case .networkFailure(let description):
                    return "Network error: \(description)"
                case .decodingError:
                    return "Failed to decode the server response."
                case .configurationError(let message):
                    return "Configuration issue: \(message)"
                case .invalidSymbol:
                    return "The stock symbol is invalid."
                case .serverError(let message):
                    return "Server encountered an error: \(message)"
                case .encodingError:
                    return "Failed to encode the request data."
                case .invalidResponse:
                    return "Invalid response received from the server."
                }
            }()
        } else if let openAIError = error as? OpenAIError {
            errorMessage = "Error generating trade plan: \(openAIError.localizedDescription)"
        } else {
            errorMessage = "Unexpected error: \(error.localizedDescription)"
        }
    }
}
