import Link from "next/link";
import { adminFetch, type AdminProductoStats } from "@/lib/admin";
import ProductosTable from "@/components/admin/ProductosTable";

export default async function AdminProductosPage() {
  const data = await adminFetch<AdminProductoStats>("/productos");

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
          <ProductosTable productos={data.productos} />
        </>
      )}
    </div>
  );
}
