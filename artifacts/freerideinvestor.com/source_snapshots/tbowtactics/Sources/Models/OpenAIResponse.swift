import Foundation
import Shared
import Core

struct OpenAIResponse: Codable, Equatable {
    let choices: [Choice]
    let usage: Usage?

    struct Choice: Codable, Equatable {
        let message: Message
    }

    struct Message: Codable, Equatable {
        let role: Role
        let content: String
    }

    enum Role: String, Codable, Equatable {
        case system
        case user
        case assistant
        case other  // For any undefined roles

        init(from decoder: Decoder) throws {
            let container = try decoder.singleValueContainer()
            let roleString = try container.decode(String.self)
            self = Role(rawValue: roleString) ?? .other
        }
    }

    struct Usage: Codable, Equatable {
        let promptTokens: Int
        let completionTokens: Int
        let totalTokens: Int
    }

    var isValid: Bool {
        !choices.isEmpty && choices.allSatisfy { !$0.message.content.isEmpty }
    }

    var concatenatedContent: String {
        choices.map { $0.message.content }.joined(separator: "\n")
    }

    var tokenSummary: String {
        guard let usage = usage else { return "No usage data available." }
        return "Prompt: \(usage.promptTokens), Completion: \(usage.completionTokens), Total: \(usage.totalTokens)"
    }

    func messages(forRole role: Role) -> [Message] {
        choices.compactMap { $0.message.role == role ? $0.message : nil }
    }

    func concatenatedContent(forRole role: Role) -> String {
        messages(forRole: role).map { $0.content }.joined(separator: "\n")
    }

    func messageCount(forRole role: Role) -> Int {
        messages(forRole: role).count
    }

    func messages(forRoles roles: [Role]) -> [Message] {
        choices.compactMap { roles.contains($0.message.role) ? $0.message : nil }
    }

    var tokenEfficiency: String {
        guard let usage = usage, !choices.isEmpty else { return "No efficiency data available." }
        let totalMessages = choices.count
        let avgTokensPerMessage = Double(usage.totalTokens) / Double(totalMessages)
        return "Average tokens per message: \(String(format: "%.2f", avgTokensPerMessage))"
    }

    // Custom initializers
    init(choices: [Choice] = [], usage: Usage? = nil) {
        self.choices = choices
        self.usage = usage
    }

    init(messages: [Message], usage: Usage? = nil) {
        self.choices = messages.map { Choice(message: $0) }
        self.usage = usage
    }
}
