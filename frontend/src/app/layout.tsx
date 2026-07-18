import type { Metadata } from "next";
import { Lora, Outfit } from "next/font/google";
import "./globals.css";
import { CartProvider } from "@/context/CartContext";
import Navbar from "@/components/layout/Navbar";
import Footer from "@/components/layout/Footer";
import { getAccessToken } from "@/lib/auth";

const lora = Lora({
  variable: "--font-lora",
  subsets: ["latin"],
  weight: ["400", "700"],
});

const outfit = Outfit({
  variable: "--font-outfit",
  subsets: ["latin"],
  weight: ["300", "400", "600", "700"],
});

export const metadata: Metadata = {
  title: "CVA Muebles",
  description: "Carpintería de Autor & Showroom — CVA Muebles",
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const isAuthenticated = Boolean(await getAccessToken());

  return (
    <html lang="es" className={`${lora.variable} ${outfit.variable} h-full antialiased`}>
      <body className="flex min-h-full flex-col bg-cva-sand text-cva-brown">
        <CartProvider>
          <Navbar isAuthenticated={isAuthenticated} />
          {children}
          <Footer />
        </CartProvider>
      </body>
    </html>
  );
}
