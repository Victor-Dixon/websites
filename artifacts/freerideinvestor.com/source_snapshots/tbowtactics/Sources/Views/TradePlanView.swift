import Foundation
import Models
import ViewModels
import Services

func showTradePlanCLI() {
    // Initialize service manager and view model
    let serviceManager = NetworkManager.shared // Ensure NetworkManager conforms to StockServiceManager
    let viewModel = TradePlanViewModel(serviceManager: serviceManager)
    
    // Prompt user for the stock symbol
    print("Enter Stock Symbol (e.g., TSLA):")
    guard let symbol = readLine(), !symbol.isEmpty else {
        print("Error: No symbol entered. Please try again.")
        return
    }
    viewModel.symbol = symbol.uppercased() // Ensure the symbol is uppercase for consistency
    
    Task {
        do {
            // Generate the trade plan
            print("\nGenerating trade plan for \(viewModel.symbol)...")
            try await viewModel.generateTradePlan()
            
            // Handle the result
            if let error = viewModel.errorMessage {
                print("\nError: \(error)")
            } else if !viewModel.tradePlan.isEmpty {
                print("\nTrade Plan for \(viewModel.symbol):")
                print(viewModel.tradePlan)
            } else {
                print("\nNo trade plan was generated. Please try again later.")
            }
        } catch {
            // Handle unexpected errors
            print("\nAn unexpected error occurred: \(error.localizedDescription)")
        }
        
        // Wait for user input to exit
        print("\nPress Enter to return to the main menu...")
        _ = readLine()
    }
}
