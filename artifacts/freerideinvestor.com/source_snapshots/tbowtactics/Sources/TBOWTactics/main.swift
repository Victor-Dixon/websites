// File: main.swift

#if os(Windows)
func mainWindowsCLI() {
    print("Running on Windows CLI")
    // CLI logic
    // readLine() etc.
}
#elseif os(iOS)
import SwiftUI
@main
struct TBOWTacticsApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
    }
}
#endif
