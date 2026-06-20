// File: Sources/TBOWTacticsCLI/main.swift

#if os(Windows)
import Foundation
import Models
import Shared
import Services
import ViewModels

func mainCLI() {
    print("Running Windows CLI version...")
    // Your while-loop CLI logic:
    var shouldContinue = true
    while shouldContinue {
        print("Options:\n1) Fetch Stock\n2) Exit\n")
        if let input = readLine(), let selection = Int(input) {
            switch selection {
            case 1:
                // ...
                print("Fetch stock logic.")
            case 2:
                shouldContinue = false
            default:
                print("Invalid option.")
            }
        }
    }
}

mainCLI()
#else
#error("This target (TBOWTacticsCLI) is for Windows CLI only.")
#endif
