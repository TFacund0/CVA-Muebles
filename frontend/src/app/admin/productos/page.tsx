import Link from "next/link";
import { Suspense } from "react";
import { adminFetch, type AdminProductoStats } from "@/lib/admin";
import ProductosTable from "@/components/admin/ProductosTable";
import SearchBox from "@/components/admin/SearchBox";
import Pagination from "@/components/admin/Pagination";

export default async function AdminProductosPage({
  searchParams,
}: {
  searchParams: Promise<{ search?: string; page_productos?: string }>;
}) {
  const { search, page_productos } = await searchParams;

  const query = new URLSearchParams();
  if (search) query.set("search", search);
  if (page_productos) query.set("page_productos", page_productos);

  const data = await adminFetch<AdminProductoStats>(`/productos${query.toString() ? `?${query}` : ""}`);

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Productos</h1>
        <Link href="/admin/productos/nuevo" className="rounded bg-black px-4 py-2 text-sm text-white">
          Nuevo producto
        </Link>
      </div>

      {!data && <p className="text-zinc-500">No se pudieron cargar los productos.</p>}

      {data && (
        <>
          <div className="mb-4 flex gap-4 text-sm text-zinc-600">
            <span>Total: {data.counts.total}</span>
            <span>Activos: {data.counts.activos}</span>
            <span>Sin stock: {data.counts.sin_stock}</span>
            <span>Archivados: {data.counts.eliminados}</span>
          </div>
          <Suspense fallback={null}>
            <SearchBox placeholder="Buscar por nombre..." pageParam="page_productos" />
          </Suspense>
          <ProductosTable productos={data.productos} />
          <Suspense fallback={null}>
            <Pagination pager={data.pager} pageParam="page_productos" />
          </Suspense>
        </>
      )}
    </div>
  );
}
