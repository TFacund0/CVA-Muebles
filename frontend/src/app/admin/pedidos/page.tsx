import Link from "next/link";
import { adminFetch, type AdminVentasResumen } from "@/lib/admin";
import PedidosTable from "@/components/admin/PedidosTable";

export default async function AdminPedidosPage() {
  const data = await adminFetch<AdminVentasResumen>("/ventas");

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Pedidos</h1>
        <Link href="/admin/pedidos/nuevo-personalizado" className="rounded bg-black px-4 py-2 text-sm text-white">
          Nuevo pedido personalizado
        </Link>
      </div>

      {!data && <p className="text-zinc-500">No se pudieron cargar los pedidos.</p>}

      {data && (
        <>
          <div className="mb-4 flex flex-wrap gap-4 text-sm text-zinc-600">
            <span>Total: {data.counts.total}</span>
            <span>Este mes: {data.counts.mensuales}</span>
            <span>Pendientes: {data.counts.pendientes}</span>
            <span>En proceso: {data.counts.en_proceso}</span>
            <span>Terminados: {data.counts.terminados}</span>
            <span>Ingresos: ${data.counts.ingresos.toFixed(2)}</span>
          </div>

          {data.solicitados.length > 0 && (
            <div className="mb-6">
              <h2 className="mb-2 font-semibold">Solicitudes pendientes de aprobación</h2>
              <PedidosTable pedidos={data.solicitados} showPrioridad={false} />
            </div>
          )}

          <h2 className="mb-2 font-semibold">Pedidos en taller</h2>
          <PedidosTable pedidos={data.ventas} showPrioridad />
        </>
      )}
    </div>
  );
}
