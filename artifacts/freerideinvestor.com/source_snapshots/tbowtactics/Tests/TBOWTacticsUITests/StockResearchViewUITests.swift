import XCTest

class StockResearchViewUITests: XCTestCase {

    override func setUpWithError() throws {
        continueAfterFailure = false
    }

    func testFetchDataButton_DisabledWhenLoading() throws {
        let app = XCUIApplication()
        app.launch()
        
        let fetchDataButton = app.buttons["Fetch Data Button"]
        let symbolTextField = app.textFields["Stock Symbol Input"]
        
        // Enter a valid symbol
        symbolTextField.tap()
        symbolTextField.typeText("AAPL")
        
        // Tap Fetch Data
        fetchDataButton.tap()
        
        // Assert that the button is disabled while loading
        XCTAssertFalse(fetchDataButton.isEnabled)
        
        // Add expectation for loading to complete, e.g., wait for a specific element
        let tradePlanText = app.staticTexts["Trade Plan:"]
        let exists = NSPredicate(format: "exists == 1")
        expectation(for: exists, evaluatedWith: tradePlanText, handler: nil)
        waitForExpectations(timeout: 5, handler: nil)
        
        // Assert that the button is enabled again
        XCTAssertTrue(fetchDataButton.isEnabled)
    }

    // Additional UI tests...
}
