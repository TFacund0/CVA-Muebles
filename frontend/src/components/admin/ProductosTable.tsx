"use client";

import Link from "next/link";
import { useMemo, useState } from "react";
import { adminMutate, adminMutateJson } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import type { AdminProducto } from "@/lib/admin";

export default function ProductosTable({ productos }: { productos: AdminProducto[] }) {
  const { error, run } = useAdminAction();
  const [search, setSearch] = useState("");

  const filtrados = useMemo(() => {
    const q = search.toLowerCase();
    return productos.filter(
      (p) => p.nombre_prod.toLowerCase().includes(q) || p.categoria.toLowerCase().includes(q)
    );
  }, [productos, search]);

  function handleEstado(id: number, accion: "eliminar" | "reactivar") {
    run(() => adminMutateJson(`/productos/${id}/estado`, "POST", { accion }));
  }

  function handleDelete(id: number) {
    if (!confirm("¿Eliminar permanentemente este producto? Esta acción no se puede deshacer.")) return;
    run(() => adminMutate(`/productos/${id}`, { method: "DELETE" }));
  }

  return (
    <div>
      <input
        type="text"
        placeholder="Buscar por nombre o categoría..."
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        className="mb-4 w-full max-w-sm rounded border px-3 py-2"
      />

      {error && <p className="mb-3 text-sm text-red-600">{error}</p>}

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b text-left text-zinc-500">
              <th className="pb-2">Nombre</th>
              <th className="pb-2">Categoría</th>
              <th className="pb-2 text-right">Precio Venta</th>
              <th className="pb-2 text-right">Stock</th>
              <th className="pb-2">Estado</th>
              <th className="pb-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            {filtrados.map((p) => (
              <tr key={p.id_producto} className="border-b">
                <td className="py-2">{p.nombre_prod}</td>
                <td className="py-2">{p.categoria}</td>
                <td className="py-2 text-right">${p.precio_vta}</td>
                <td className="py-2 text-right">{p.stock}</td>
                <td className="py-2">{p.eliminado === "SI" ? "Archivado" : "Activo"}</td>
                <td className="py-2">
                  <div className="flex flex-wrap gap-2">
                    <Link href={`/admin/productos/${p.id_producto}/editar`} className="text-blue-600 hover:underline">
                      Editar
                    </Link>
                    {p.eliminado === "SI" ? (
                      <button onClick={() => handleEstado(p.id_producto, "reactivar")} className="text-green-600 hover:underline">
                        Reactivar
                      </button>
                    ) : (
                      <button onClick={() => handleEstado(p.id_producto, "eliminar")} className="text-amber-600 hover:underline">
                        Archivar
                      </button>
                    )}
                    <button onClick={() => handleDelete(p.id_producto)} className="text-red-600 hover:underline">
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
