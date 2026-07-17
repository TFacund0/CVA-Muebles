import Link from "next/link";

const LINKS = [
  { href: "/admin", label: "Dashboard" },
  { href: "/admin/pedidos", label: "Pedidos" },
  { href: "/admin/consultas", label: "Consultas" },
  { href: "/admin/galeria", label: "Galería" },
  { href: "/admin/productos", label: "Productos" },
  { href: "/admin/categorias", label: "Categorías" },
  { href: "/admin/usuarios", label: "Usuarios" },
];

export default function AdminSidebar() {
  return (
    <aside className="w-56 flex-shrink-0 border-r p-4">
      <p className="mb-6 text-lg font-semibold">Panel Admin</p>
      <nav className="flex flex-col gap-1 text-sm">
        {LINKS.map((link) => (
          <Link key={link.href} href={link.href} className="rounded px-3 py-2 hover:bg-zinc-100">
            {link.label}
          </Link>
        ))}
      </nav>
      <div className="mt-8 border-t pt-4">
        <Link href="/" className="text-sm text-zinc-500 hover:underline">
          &larr; Volver al sitio
        </Link>
      </div>
    </aside>
  );
}
