import type { Metadata, Viewport } from "next";
import { Inter } from "next/font/google";
import "./globals.css";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
  display: "swap",
});

export const metadata: Metadata = {
  title: "MaskZero Studio | Spark Animation Engine",
  description:
    "Generate cinematic Spark character animations from prompts, images, and clips inside the MaskZero comic universe.",
  keywords: [
    "MaskZero",
    "Spark animation",
    "text to video",
    "character portrait",
    "AI filmmaking",
    "Spark Motion",
  ],
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  themeColor: "#20122d",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={inter.variable}>
      <head>
        <link rel="stylesheet" href="/assets/css/maskzero-comic-theme.css" />
      </head>
      <body className="maskzero-comic-skin">{children}</body>
    </html>
  );
}
