import Image from "next/image";
import Link from "next/link";
import type { Producto } from "@/lib/api";

export default function ProductoCard({ producto }: { producto: Producto }) {
  return (
    <Link
      href={`/productos/${producto.id_producto}`}
      className="overflow-hidden rounded-lg border transition hover:shadow-md"
    >
      <div className="relative aspect-square bg-zinc-100">
        {producto.imagen ? (
          <Image
            src={producto.imagen}
            alt={producto.nombre_prod}
            fill
            sizes="(max-width: 768px) 50vw, 33vw"
            className="object-cover"
          />
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-zinc-400">
            Sin imagen
          </div>
        )}
      </div>
      <div className="p-4">
        <h2 className="font-medium">{producto.nombre_prod}</h2>
        <p className="text-sm text-zinc-500">{producto.categoria}</p>
        <p className="mt-2 font-semibold">${producto.precio_vta}</p>
      </div>
    </Link>
  );
}
