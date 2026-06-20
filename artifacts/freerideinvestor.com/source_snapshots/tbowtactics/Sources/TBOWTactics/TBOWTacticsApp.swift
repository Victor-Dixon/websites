// File: TBOWTacticsApp.swift
import SwiftUI
import Models
import Shared
import Services
import ViewModels

@main
struct TBOWTacticsApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
    }
}

struct ContentView: View {
    @StateObject private var alertsViewModel = AlertsViewModel()

    var body: some View {
        VStack {
            Text("iOS SwiftUI Home Screen").bold()
            Button("Load Alerts") {
                alertsViewModel.loadAlerts()
            }
        }
    }
}
