import XCTest

class ExtensionsTests: XCTestCase {
    func testStandardDateFormat() {
        let testDate = Date(timeIntervalSince1970: 0) // Jan 1, 1970
        let formattedDate = DateFormatter.standard.string(from: testDate)
        XCTAssertEqual(formattedDate, "1970-01-01")
    }

    func testFullDateFormat() {
        let testDate = Date(timeIntervalSince1970: 0) // Jan 1, 1970
        let formattedDate = DateFormatter.full.string(from: testDate)
        XCTAssertEqual(formattedDate, "Thursday, January 1, 1970") // Locale-dependent
    }

    func testTimeOnlyFormat() {
        let testDate = Date(timeIntervalSince1970: 3600) // 1 hour past midnight
        let formattedTime = DateFormatter.timeOnly.string(from: testDate)
        XCTAssertEqual(formattedTime, "01:00:00")
    }

    func testCurrencyFormat() {
        let amount: Double = 1234.56
        let formattedAmount = amount.toCurrency(locale: Locale(identifier: "en_US"), currencyCode: "USD")
        XCTAssertEqual(formattedAmount, "$1,234.56")
    }
}
