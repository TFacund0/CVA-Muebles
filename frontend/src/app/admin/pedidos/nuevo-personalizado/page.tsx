"use client";

import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { adminMutate } from "@/lib/adminClient";

export default function NuevoPedidoPersonalizadoPage() {
  const router = useRouter();
  const [nombreCliente, setNombreCliente] = useState("");
  const [detallesObra, setDetallesObra] = useState("");
  const [totalVenta, setTotalVenta] = useState("");
  const [montoSena, setMontoSena] = useState("");
  const [imagen, setImagen] = useState<File | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setLoading(true);

    const formData = new FormData();
    formData.append("nombre_cliente", nombreCliente);
    formData.append("detalles_obra", detallesObra);
    formData.append("total_venta", totalVenta);
    formData.append("monto_sena", montoSena || "0");
    if (imagen) formData.append("imagen_referencia", imagen);

    const result = await adminMutate("/ventas/personalizado", { method: "POST", body: formData });

    if (result.unauthorized) return router.push("/login");
    if (!result.ok) {
      setError(result.message);
      setLoading(false);
      return;
    }

    router.push("/admin/pedidos");
    router.refresh();
  }

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Nuevo pedido personalizado</h1>

      <form onSubmit={handleSubmit} className="flex max-w-md flex-col gap-3">
        <input
          type="text"
          placeholder="Nombre del cliente"
          value={nombreCliente}
          onChange={(e) => setNombreCliente(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <textarea
          placeholder="Detalles de la obra"
          value={detallesObra}
          onChange={(e) => setDetallesObra(e.target.value)}
          rows={5}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="number"
          step="0.01"
          placeholder="Total del pedido"
          value={totalVenta}
          onChange={(e) => setTotalVenta(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="number"
          step="0.01"
          placeholder="Seña inicial (opcional)"
          value={montoSena}
          onChange={(e) => setMontoSena(e.target.value)}
          className="rounded border px-3 py-2"
        />
        <div>
          <label className="mb-1 block text-sm text-zinc-600">Imagen de referencia (opcional)</label>
          <input
            type="file"
            accept="image/jpeg,image/png,image/webp"
            onChange={(e) => setImagen(e.target.files?.[0] ?? null)}
          />
        </div>

        {error && <p className="text-sm text-red-600">{error}</p>}

        <button
          type="submit"
          disabled={loading}
          className="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
        >
          {loading ? "Guardando..." : "Crear pedido"}
        </button>
      </form>
    </div>
  );
}
