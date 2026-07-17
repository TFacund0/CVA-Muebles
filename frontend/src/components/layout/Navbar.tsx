"use client";

import Link from "next/link";
import { useCart } from "@/context/CartContext";

const NAV_LINKS = [
  { href: "/", label: "Catálogo" },
  { href: "/quienes-somos", label: "Quiénes Somos" },
  { href: "/beneficios", label: "Beneficios" },
  { href: "/comercializacion", label: "Comercialización" },
  { href: "/galeria", label: "Galería" },
  { href: "/contacto", label: "Contacto" },
];

export default function Navbar({ isAuthenticated }: { isAuthenticated: boolean }) {
  const { totalItems } = useCart();

  return (
    <header className="border-b">
      <nav className="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-4">
        <Link href="/" className="text-lg font-semibold">
          CVA Muebles
        </Link>

        <div className="flex flex-wrap items-center gap-4 text-sm">
          {NAV_LINKS.map((link) => (
            <Link key={link.href} href={link.href} className="hover:underline">
              {link.label}
            </Link>
          ))}

          <Link href="/carrito" className="hover:underline">
            Carrito{totalItems > 0 ? ` (${totalItems})` : ""}
          </Link>

          {isAuthenticated ? (
            <>
              <Link href="/favoritos" className="hover:underline">
                Favoritos
              </Link>
              <Link href="/pedidos" className="hover:underline">
                Mis Pedidos
              </Link>
              <Link href="/perfil" className="hover:underline">
                Mi Perfil
              </Link>
            </>
          ) : (
            <>
              <Link href="/login" className="hover:underline">
                Iniciar sesión
              </Link>
              <Link href="/registro" className="hover:underline">
                Registrarse
              </Link>
            </>
          )}
        </div>
      </nav>
    </header>
  );
}
