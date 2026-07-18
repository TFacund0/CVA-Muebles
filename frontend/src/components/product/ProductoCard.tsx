import Image from "next/image";
import Link from "next/link";
import type { Producto } from "@/lib/api";
import AddToCartButton from "./AddToCartButton";
import FavoritoButton from "./FavoritoButton";

export default function ProductoCard({
  producto,
  priority = false,
}: {
  producto: Producto;
  priority?: boolean;
}) {
  return (
    <div className="group overflow-hidden rounded-4xl bg-white p-4 shadow-[0_15px_35px_rgba(0,0,0,0.05)] transition duration-300 ease-out hover:-translate-y-2 hover:shadow-[0_40px_80px_rgba(62,39,35,0.12)]">
      <Link href={`/productos/${producto.id_producto}`} className="relative block">
        <div className="relative aspect-square overflow-hidden rounded-2xl bg-cva-parchment">
          {producto.imagen ? (
            <Image
              src={producto.imagen}
              alt={producto.nombre_prod}
              fill
              sizes="(max-width: 768px) 50vw, 33vw"
              className="object-cover transition duration-300 group-hover:scale-105"
              priority={priority}
            />
          ) : (
            <div className="flex h-full items-center justify-center text-sm text-cva-text-muted">
              Sin imagen
            </div>
          )}
          {producto.categoria && (
            <span className="absolute top-3 left-3 rounded-full bg-black/40 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
              {producto.categoria}
            </span>
          )}
        </div>
      </Link>

      <div className="relative -mt-8 mr-1 flex justify-end">
        <FavoritoButton productoId={producto.id_producto} circular />
      </div>

      <div className="px-2 pt-1 pb-1">
        <Link href={`/productos/${producto.id_producto}`}>
          <h2 className="font-heading text-lg font-semibold text-cva-brown">{producto.nombre_prod}</h2>
        </Link>
        <p className="mt-1 text-xl font-extrabold text-cva-vivid">${producto.precio_vta}</p>
      </div>

      <div className="mt-3 flex items-center gap-2 px-2 pb-1">
        <Link href={`/productos/${producto.id_producto}`} className="btn-artisan-gold flex-1 text-center">
          Ver Detalles
        </Link>
        <AddToCartButton producto={producto} compact />
      </div>
    </div>
  );
}
