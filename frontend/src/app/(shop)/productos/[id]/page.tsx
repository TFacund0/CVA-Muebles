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
    <main className="mx-auto max-w-5xl px-6 py-12">
      <Link href="/" className="text-sm font-semibold text-cva-gold hover:underline">
        &larr; CATÁLOGO
      </Link>

      <div className="mt-4 overflow-hidden rounded-4xl bg-white shadow-[0_15px_35px_rgba(0,0,0,0.05)]">
        <div className="grid gap-0 lg:grid-cols-2">
          <div className="p-6">
            {producto.imagen && (
              <div className="relative aspect-square overflow-hidden rounded-2xl bg-cva-parchment shadow-sm">
                <Image
                  src={producto.imagen}
                  alt={producto.nombre_prod}
                  fill
                  sizes="(max-width: 1024px) 100vw, 50vw"
                  className="object-cover"
                  priority
                />
              </div>
            )}

            {producto.galeria && producto.galeria.length > 0 && (
              <div className="mt-3 flex gap-2 overflow-x-auto">
                {producto.galeria.map((foto) => (
                  <div
                    key={foto.id}
                    className="relative aspect-square w-20 shrink-0 overflow-hidden rounded-xl bg-cva-parchment"
                  >
                    <Image src={foto.imagen} alt="" fill sizes="80px" className="object-cover" />
                  </div>
                ))}
              </div>
            )}
          </div>

          <div className="flex flex-col p-6 lg:p-10">
            {producto.categoria && (
              <span className="mb-3 inline-block w-fit rounded-full bg-cva-gold/15 px-3 py-1 text-xs font-bold tracking-wide text-cva-gold uppercase">
                {producto.categoria}
              </span>
            )}
            <h1 className="font-heading text-3xl font-bold text-cva-brown">{producto.nombre_prod}</h1>
            <div className="my-4 h-1 w-16 rounded-full bg-cva-gold" />

            <p className="text-sm font-semibold tracking-wide text-cva-text-muted uppercase">
              Inversión artesanal
            </p>
            <p className="font-heading text-4xl font-extrabold text-cva-vivid">${producto.precio_vta}</p>

            <p className="mt-3 flex items-center gap-2 text-sm text-cva-brown-light">
              🔨 Fabricación bajo pedido (Consultar tiempos de entrega)
            </p>

            {producto.descripcion && (
              <p className="mt-6 text-cva-brown-light">{producto.descripcion}</p>
            )}

            <div className="mt-auto flex flex-wrap gap-3 pt-8">
              <AddToCartButton producto={producto} />
              <FavoritoButton productoId={producto.id_producto} />
              <a
                href={`https://wa.me/5493794098511?text=${encodeURIComponent(
                  `Hola! Quiero consultar por: ${producto.nombre_prod}`,
                )}`}
                target="_blank"
                rel="noreferrer"
                className="btn-whatsapp-artisan"
              >
                💬 Consultar por WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-10 grid gap-6 sm:grid-cols-3">
        <div className="rounded-2xl bg-white p-6 text-center shadow-[0_15px_35px_rgba(0,0,0,0.05)]">
          <p className="text-2xl">🚚</p>
          <p className="font-heading mt-2 font-semibold text-cva-brown">Envío Seguro</p>
        </div>
        <div className="rounded-2xl bg-white p-6 text-center shadow-[0_15px_35px_rgba(0,0,0,0.05)]">
          <p className="text-2xl">🛡️</p>
          <p className="font-heading mt-2 font-semibold text-cva-brown">Garantía de Obra</p>
        </div>
        <div className="rounded-2xl bg-white p-6 text-center shadow-[0_15px_35px_rgba(0,0,0,0.05)]">
          <p className="text-2xl">🌿</p>
          <p className="font-heading mt-2 font-semibold text-cva-brown">Madera Sustentable</p>
        </div>
      </div>
    </main>
  );
}
