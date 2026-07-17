import { redirect } from "next/navigation";
import ProductoCard from "@/components/product/ProductoCard";
import { apiFetchAuthed } from "@/lib/auth";
import type { Favorito } from "@/lib/api";

export default async function FavoritosPage() {
  const favoritos = await apiFetchAuthed<Favorito[]>("/favoritos");

  if (favoritos === null) {
    redirect("/login");
  }

  return (
    <main className="mx-auto max-w-5xl px-6 py-12">
      <h1 className="mb-8 text-3xl font-semibold">Mis Favoritos</h1>

      {favoritos.length === 0 && (
        <p className="text-zinc-500">Todavía no marcaste ningún producto como favorito.</p>
      )}

      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        {favoritos.map((fav) => (
          <ProductoCard
            key={fav.id}
            producto={{
              id_producto: fav.producto_id,
              nombre_prod: fav.nombre_prod,
              imagen: fav.imagen,
              categoria_id: 0,
              categoria: fav.categoria,
              precio: fav.precio_vta,
              precio_vta: fav.precio_vta,
              stock: 0,
              descripcion: fav.descripcion,
            }}
          />
        ))}
      </div>
    </main>
  );
}
