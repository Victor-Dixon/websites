import type { Metadata, Viewport } from "next";
import { Inter } from "next/font/google";
import "./globals.css";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
  display: "swap",
});

export const metadata: Metadata = {
  title: "SkyMotion AI | AI Animated Video Studio",
  description:
    "Generate cinematic animated videos from prompts, images, and clips with SkyMotion AI's DreamMotion filmmaking engine.",
  keywords: [
    "AI animation",
    "text to video",
    "image animation",
    "AI filmmaking",
    "video generation",
    "DreamMotion",
  ],
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  themeColor: "#030712",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={inter.variable}>
      <body>{children}</body>
    </html>
  );
}
