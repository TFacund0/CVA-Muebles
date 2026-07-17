import Link from "next/link";
import { Suspense } from "react";
import { adminFetch, type AdminVentasResumen } from "@/lib/admin";
import PedidosTable from "@/components/admin/PedidosTable";
import SearchBox from "@/components/admin/SearchBox";
import Pagination from "@/components/admin/Pagination";

export default async function AdminPedidosPage({
  searchParams,
}: {
  searchParams: Promise<{ search?: string; page_ventas?: string }>;
}) {
  const { search, page_ventas } = await searchParams;

  const query = new URLSearchParams();
  if (search) query.set("search", search);
  if (page_ventas) query.set("page_ventas", page_ventas);

  const data = await adminFetch<AdminVentasResumen>(`/ventas${query.toString() ? `?${query}` : ""}`);

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
          <Suspense fallback={null}>
            <SearchBox placeholder="Buscar por cliente o N° de pedido..." pageParam="page_ventas" />
          </Suspense>
          <PedidosTable pedidos={data.ventas} showPrioridad />
          <Suspense fallback={null}>
            <Pagination pager={data.pager} pageParam="page_ventas" />
          </Suspense>
        </>
      )}
    </div>
  );
}
