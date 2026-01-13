import "./globals.css";
import type { Metadata } from "next";
import { Inter } from "next/font/google";

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: "FreeRideInvestor",
  description: "Modern trading + systems journal.",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className="dark">
      <body className={inter.className + " bg-zinc-950 text-zinc-50 antialiased"}>
        {children}
      </body>
    </html>
  );
}
