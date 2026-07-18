"use client";

import Image from "next/image";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState } from "react";
import { useCart } from "@/context/CartContext";

const NAV_LINKS = [
  { href: "/", label: "Inicio" },
  { href: "/productos", label: "Productos" },
  { href: "/comercializacion", label: "Comercialización" },
  { href: "/quienes-somos", label: "Información" },
  { href: "/galeria", label: "Galería" },
  { href: "/contacto", label: "Contacto" },
];

export default function Navbar({ isAuthenticated }: { isAuthenticated: boolean }) {
  const { totalItems } = useCart();
  const pathname = usePathname();
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <>
      <nav
        className="sticky top-0 z-50 flex min-h-20 items-center border-b-[3px] border-cva-gold bg-cva-brown bg-cover bg-center shadow-[0_4px_15px_rgba(0,0,0,0.3)]"
        style={{
          backgroundImage:
            "linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.5)), url(/img/madera.jpg)",
        }}
      >
        <div className="flex w-full items-center justify-between px-3 lg:px-12">
          {/* Mobile: hamburguesa + carrito */}
          <div className="flex flex-1 items-center gap-2 lg:hidden">
            <button
              type="button"
              aria-label="Abrir menú"
              onClick={() => setMobileOpen((v) => !v)}
              className="flex h-12 w-12 items-center justify-center rounded-full border border-white/25 bg-white/12 text-xl text-white backdrop-blur-sm transition hover:-translate-y-0.5 hover:scale-105 hover:border-cva-gold hover:bg-cva-gold"
            >
              ☰
            </button>
            {isAuthenticated && (
              <Link
                href="/carrito"
                className="relative flex h-12 w-12 items-center justify-center rounded-full border border-white/25 bg-white/12 text-lg text-white backdrop-blur-sm transition hover:-translate-y-0.5 hover:scale-105 hover:border-cva-gold hover:bg-cva-gold"
              >
                🛒
                {totalItems > 0 && (
                  <span className="absolute -top-1 -right-1 rounded-full bg-red-600 px-1.5 text-[0.6rem] font-bold text-white">
                    {totalItems}
                  </span>
                )}
              </Link>
            )}
          </div>

          {/* Logo */}
          <div className="flex flex-1 items-center justify-center lg:flex-none lg:justify-start">
            <Link href="/" className="m-0 flex items-center gap-2">
              <Image src="/img/logo.png" alt="Logo" width={50} height={50} className="w-[50px]" />
              <h1 className="font-heading m-0 hidden text-2xl font-bold text-white [text-shadow:2px_2px_4px_rgba(0,0,0,0.5)] lg:block">
                CVA Muebles
              </h1>
            </Link>
          </div>

          {/* Links desktop */}
          <div className="hidden flex-[2] justify-center lg:flex">
            <ul className="flex list-none items-center gap-1">
              {NAV_LINKS.map((link) => {
                const active = link.href === "/" ? pathname === "/" : pathname.startsWith(link.href);
                return (
                  <li key={link.href}>
                    <Link
                      href={link.href}
                      className={`group relative inline-block px-4 py-6 text-[0.85rem] font-semibold tracking-wide text-white uppercase transition ${
                        active ? "text-cva-gold-light" : "hover:text-cva-gold-light"
                      }`}
                    >
                      {link.label}
                      <span
                        className={`absolute bottom-4 left-1/2 h-0.5 -translate-x-1/2 bg-cva-gold transition-all duration-300 ${
                          active ? "w-[70%]" : "w-0 group-hover:w-[70%]"
                        }`}
                      />
                    </Link>
                  </li>
                );
              })}
            </ul>
          </div>

          {/* Derecha */}
          <div className="flex flex-1 items-center justify-end gap-2">
            {isAuthenticated && (
              <Link
                href="/carrito"
                className="relative hidden h-12 w-12 items-center justify-center rounded-full border border-white/25 bg-white/12 text-lg text-white backdrop-blur-sm transition hover:-translate-y-0.5 hover:scale-105 hover:border-cva-gold hover:bg-cva-gold lg:flex"
              >
                🛒
                {totalItems > 0 && (
                  <span className="absolute -top-1 -right-1 rounded-full bg-red-600 px-1.5 text-[0.6rem] font-bold text-white">
                    {totalItems}
                  </span>
                )}
              </Link>
            )}

            {!isAuthenticated ? (
              <div className="hidden items-center rounded-full border border-white/30 bg-white/10 px-4.5 py-1.5 backdrop-blur-sm transition hover:border-cva-gold hover:bg-white/20 lg:flex">
                <Link
                  href="/login"
                  className="rounded-full px-2 py-1 text-xs font-bold tracking-wide text-white uppercase transition hover:bg-white/10 hover:text-cva-gold"
                >
                  Ingresar
                </Link>
                <div className="mx-3 h-3.5 w-px bg-white/30" />
                <Link
                  href="/registro"
                  className="rounded-full px-2 py-1 text-xs font-bold tracking-wide text-white uppercase transition hover:bg-white/10 hover:text-cva-gold"
                >
                  Registrarse
                </Link>
              </div>
            ) : (
              <button
                type="button"
                aria-label="Mi cuenta"
                onClick={() => setMobileOpen((v) => !v)}
                className="hidden h-12 w-12 items-center justify-center rounded-full border border-white/25 bg-white/12 text-lg text-white backdrop-blur-sm transition hover:-translate-y-0.5 hover:scale-105 hover:border-cva-gold hover:bg-cva-gold lg:flex"
              >
                👤
              </button>
            )}
          </div>
        </div>
      </nav>

      {/* Menú lateral (mobile + cuenta) */}
      {mobileOpen && (
        <div className="fixed inset-0 z-50 flex justify-end">
          <div className="absolute inset-0 bg-black/50" onClick={() => setMobileOpen(false)} />
          <div className="relative flex h-full w-[85%] max-w-[350px] flex-col overflow-y-auto bg-cva-parchment shadow-2xl">
            <div className="flex items-center justify-between border-b bg-white p-4 shadow-sm">
              <div className="flex items-center gap-2">
                <Image src="/img/logo.png" alt="Logo" width={35} height={35} />
                <h5 className="font-heading font-bold text-cva-brown">CVA Muebles</h5>
              </div>
              <button onClick={() => setMobileOpen(false)} aria-label="Cerrar" className="text-xl text-cva-brown">
                ✕
              </button>
            </div>

            <div className="border-b p-3 lg:hidden">
              <div className="overflow-hidden rounded-2xl border shadow-sm">
                {NAV_LINKS.map((link) => (
                  <Link
                    key={link.href}
                    href={link.href}
                    onClick={() => setMobileOpen(false)}
                    className="block border-b bg-white px-4 py-3 text-sm text-cva-brown last:border-b-0 hover:bg-cva-sand"
                  >
                    {link.label}
                  </Link>
                ))}
              </div>
            </div>

            <div className="p-3">
              {isAuthenticated ? (
                <div className="overflow-hidden rounded-2xl border bg-white shadow-sm">
                  <Link
                    href="/perfil"
                    onClick={() => setMobileOpen(false)}
                    className="block border-b px-4 py-3 text-sm text-cva-brown hover:bg-cva-sand"
                  >
                    Mi Perfil
                  </Link>
                  <Link
                    href="/favoritos"
                    onClick={() => setMobileOpen(false)}
                    className="block border-b px-4 py-3 text-sm text-cva-brown hover:bg-cva-sand"
                  >
                    Favoritos
                  </Link>
                  <Link
                    href="/pedidos"
                    onClick={() => setMobileOpen(false)}
                    className="block px-4 py-3 text-sm font-bold text-cva-brown hover:bg-cva-sand"
                  >
                    Mis Compras
                  </Link>
                </div>
              ) : (
                <div className="grid gap-2 p-2 text-center">
                  <Link href="/login" onClick={() => setMobileOpen(false)} className="btn-brown-solid">
                    Iniciar Sesión
                  </Link>
                  <Link href="/registro" onClick={() => setMobileOpen(false)} className="btn-outline-brown">
                    Registrarse
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </>
  );
}
