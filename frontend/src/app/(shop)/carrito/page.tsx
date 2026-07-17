"use client";

import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useCart } from "@/context/CartContext";
import { useApiAction } from "@/lib/useApiAction";
import { clientFetchJson } from "@/lib/clientFetch";

export default function CarritoPage() {
  const { items, totalPrecio, setCantidad, removeItem, clear } = useCart();
  const router = useRouter();
  const { error, loading, run } = useApiAction();
  const [observaciones, setObservaciones] = useState("");

  async function handleCheckout() {
    const result = await run(() =>
      clientFetchJson("/api/ventas", "POST", {
        items: items.map((i) => ({ producto_id: i.producto_id, cantidad: i.cantidad })),
        observaciones,
      })
    );

    if (result?.ok) {
      clear();
      router.push("/pedidos");
    }
  }

  if (items.length === 0) {
    return (
      <main className="mx-auto max-w-3xl px-6 py-12">
        <h1 className="mb-4 text-3xl font-semibold">Carrito</h1>
        <p className="text-zinc-500">
          Tu carrito está vacío.{" "}
          <Link href="/" className="underline">
            Ver catálogo
          </Link>
        </p>
      </main>
    );
  }

  return (
    <main className="mx-auto max-w-3xl px-6 py-12">
      <h1 className="mb-6 text-3xl font-semibold">Carrito</h1>

      <div className="flex flex-col gap-4">
        {items.map((item) => (
          <div key={item.producto_id} className="flex items-center gap-4 rounded-lg border p-4">
            <div className="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded bg-zinc-100">
              {item.imagen && (
                <Image src={item.imagen} alt={item.nombre_prod} fill sizes="80px" className="object-cover" />
              )}
            </div>
            <div className="flex-1">
              <p className="font-medium">{item.nombre_prod}</p>
              <p className="text-sm text-zinc-500">${item.precio_vta}</p>
            </div>
            <input
              type="number"
              min={1}
              value={item.cantidad}
              onChange={(e) => setCantidad(item.producto_id, Number(e.target.value))}
              className="w-16 rounded border px-2 py-1 text-center"
            />
            <button
              onClick={() => removeItem(item.producto_id)}
              className="text-sm text-red-600 hover:underline"
            >
              Quitar
            </button>
          </div>
        ))}
      </div>

      <div className="mt-6">
        <label className="mb-1 block text-sm font-medium">Observaciones (opcional)</label>
        <textarea
          value={observaciones}
          onChange={(e) => setObservaciones(e.target.value)}
          rows={3}
          className="w-full rounded border px-3 py-2"
        />
      </div>

      <div className="mt-6 flex items-center justify-between border-t pt-4">
        <p className="text-xl font-semibold">Total: ${totalPrecio.toFixed(2)}</p>
        <button
          onClick={handleCheckout}
          disabled={loading}
          className="rounded bg-black px-6 py-2 text-white disabled:opacity-50"
        >
          {loading ? "Procesando..." : "Confirmar Pedido"}
        </button>
      </div>

      {error && <p className="mt-3 text-sm text-red-600">{error}</p>}
    </main>
  );
}
