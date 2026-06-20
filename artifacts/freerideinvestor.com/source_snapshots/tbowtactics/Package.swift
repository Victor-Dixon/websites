// swift-tools-version:5.5
import PackageDescription

let package = Package(
    name: "TBOWTactics",
    platforms: [
        .macOS(.v10_15), // macOS platform
        .iOS(.v14)       // iOS platform
    ],
    products: [
        // Main executable product for UI
        .executable(
            name: "TBOWTactics",
            targets: ["TBOWTactics"]
        ),
        // CLI executable product
        .executable(
            name: "TBOWTacticsCLI",
            targets: ["TBOWTacticsCLI"]
        )
    ],
    dependencies: [
        // Add external dependencies here if any
    ],
    targets: [
        // Core module
        .target(
            name: "Core",
            dependencies: ["Utilities"], // Add Utilities as a dependency
            path: "Sources/Core"
        ),
        
        // Shared module
        .target(
            name: "Shared",
            dependencies: ["Core"],
            path: "Sources/Shared"
        ),
        
        // Models module
        .target(
            name: "Models",
            dependencies: ["Shared", "Core"],
            path: "Sources/Models"
        ),
        
        // Utilities module
        .target(
            name: "Utilities",
            dependencies: [],
            path: "Sources/Utilities"
        ),

        // Services module
        .target(
            name: "Services",
            dependencies: ["Shared", "Models", "Utilities"],
            path: "Sources/Services"
        ),
        
        // ViewModels module
        .target(
            name: "ViewModels",
            dependencies: ["Models", "Services"],
            path: "Sources/ViewModels"
        ),

        // Main UI executable target
        .executableTarget(
            name: "TBOWTactics",
            dependencies: ["Core", "Shared", "Models", "Services", "ViewModels", "Utilities"],
            path: "Sources/TBOWTactics"
        ),

        // CLI executable target
        .executableTarget(
            name: "TBOWTacticsCLI",
            dependencies: ["Core", "Shared", "Models", "Services", "Utilities", "ViewModels"],
            path: "Sources/TBOWTacticsCLI"
        ),

        // Unit tests
        .testTarget(
            name: "TBOWTacticsTests",
            dependencies: ["TBOWTactics"],
            path: "Tests/TBOWTacticsTests"
        ),
        
        // UI Tests
        .testTarget(
            name: "TBOWTacticsUITests",
            dependencies: ["TBOWTactics"],
            path: "Tests/TBOWTacticsUITests"
        )
    ]
)
