import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import AddToCartButton from "@/components/product/AddToCartButton";
import FavoritoButton from "@/components/product/FavoritoButton";
import { getProducto } from "@/lib/api";

export default async function ProductoPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  let producto;
  try {
    producto = await getProducto(id);
  } catch {
    notFound();
  }

  return (
    <main className="mx-auto max-w-3xl px-6 py-12">
      <Link href="/" className="text-sm underline">
        &larr; Volver al catálogo
      </Link>

      {producto.imagen && (
        <div className="relative mt-4 aspect-video overflow-hidden rounded-lg bg-zinc-100">
          <Image
            src={producto.imagen}
            alt={producto.nombre_prod}
            fill
            sizes="768px"
            className="object-cover"
            priority
          />
        </div>
      )}

      {producto.galeria && producto.galeria.length > 0 && (
        <div className="mt-3 grid grid-cols-4 gap-2">
          {producto.galeria.map((foto) => (
            <div key={foto.id} className="relative aspect-square overflow-hidden rounded bg-zinc-100">
              <Image src={foto.imagen} alt="" fill sizes="150px" className="object-cover" />
            </div>
          ))}
        </div>
      )}

      <h1 className="mt-6 text-3xl font-semibold">{producto.nombre_prod}</h1>
      <p className="text-zinc-500">{producto.categoria}</p>
      <p className="mt-4 text-2xl font-semibold">${producto.precio_vta}</p>
      {producto.descripcion && <p className="mt-4 text-zinc-700">{producto.descripcion}</p>}
      <p className="mt-4 text-sm text-zinc-500">Stock disponible: {producto.stock}</p>

      <div className="mt-4 flex gap-3">
        <AddToCartButton producto={producto} />
        <FavoritoButton productoId={producto.id_producto} />
      </div>
    </main>
  );
}
