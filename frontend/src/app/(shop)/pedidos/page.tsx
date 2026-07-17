import Link from "next/link";
import { redirect } from "next/navigation";
import { apiFetchAuthed } from "@/lib/auth";
import { formatEstado, formatFecha } from "@/lib/format";
import type { VentaResumen } from "@/lib/api";

export default async function PedidosPage() {
  const ventas = await apiFetchAuthed<VentaResumen[]>("/ventas");

  if (ventas === null) {
    redirect("/login");
  }

  return (
    <main className="mx-auto max-w-3xl px-6 py-12">
      <h1 className="mb-8 text-3xl font-semibold">Mis Pedidos</h1>

      {ventas.length === 0 && <p className="text-zinc-500">Todavía no tenés pedidos registrados.</p>}

      <div className="flex flex-col gap-4">
        {ventas.map((venta) => (
          <Link
            key={venta.id}
            href={`/pedidos/${venta.id}`}
            className="flex items-center justify-between rounded-lg border p-4 transition hover:shadow-md"
          >
            <div>
              <p className="font-medium">Pedido #{venta.id}</p>
              <p className="text-sm text-zinc-500">{formatFecha(venta.fecha)}</p>
            </div>
            <div className="text-right">
              <p className="font-semibold">${venta.total_venta}</p>
              <p className="text-sm text-zinc-500">{formatEstado(venta.estado)}</p>
            </div>
          </Link>
        ))}
      </div>
    </main>
  );
}
