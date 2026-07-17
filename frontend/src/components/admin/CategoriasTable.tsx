"use client";

import { useState, type FormEvent } from "react";
import { adminMutate, adminMutateJson } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import type { AdminCategoria } from "@/lib/admin";

export default function CategoriasTable({ categorias }: { categorias: AdminCategoria[] }) {
  const { error, run } = useAdminAction();
  const [nuevaDescripcion, setNuevaDescripcion] = useState("");
  const [editandoId, setEditandoId] = useState<number | null>(null);
  const [editDescripcion, setEditDescripcion] = useState("");

  async function handleCrear(e: FormEvent) {
    e.preventDefault();
    const result = await run(() => adminMutateJson("/categorias", "POST", { descripcion: nuevaDescripcion }));
    if (result?.ok) setNuevaDescripcion("");
  }

  async function handleGuardarEdicion(id: number) {
    const result = await run(() => adminMutateJson(`/categorias/${id}`, "PUT", { descripcion: editDescripcion }));
    if (result?.ok) setEditandoId(null);
  }

  function handleToggle(id: number) {
    run(() => adminMutate(`/categorias/${id}/toggle`, { method: "POST" }));
  }

  function handleEliminar(id: number) {
    if (!confirm("¿Eliminar esta categoría?")) return;
    run(() => adminMutate(`/categorias/${id}`, { method: "DELETE" }));
  }

  return (
    <div>
      <form onSubmit={handleCrear} className="mb-6 flex max-w-md gap-2">
        <input
          type="text"
          placeholder="Nueva categoría"
          value={nuevaDescripcion}
          onChange={(e) => setNuevaDescripcion(e.target.value)}
          className="flex-1 rounded border px-3 py-2"
          required
        />
        <button type="submit" className="rounded bg-black px-4 py-2 text-sm text-white">
          Crear
        </button>
      </form>

      {error && <p className="mb-3 text-sm text-red-600">{error}</p>}

      <table className="w-full text-sm">
        <thead>
          <tr className="border-b text-left text-zinc-500">
            <th className="pb-2">Descripción</th>
            <th className="pb-2 text-right">Productos</th>
            <th className="pb-2">Estado</th>
            <th className="pb-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          {categorias.map((cat) => (
            <tr key={cat.id_categoria} className="border-b">
              <td className="py-2">
                {editandoId === cat.id_categoria ? (
                  <input
                    type="text"
                    value={editDescripcion}
                    onChange={(e) => setEditDescripcion(e.target.value)}
                    className="rounded border px-2 py-1"
                  />
                ) : (
                  cat.descripcion
                )}
              </td>
              <td className="py-2 text-right">
                {cat.productos_activos}/{cat.total_productos}
              </td>
              <td className="py-2">{cat.activo ? "Activa" : "Inactiva"}</td>
              <td className="py-2">
                <div className="flex flex-wrap gap-2">
                  {editandoId === cat.id_categoria ? (
                    <button onClick={() => handleGuardarEdicion(cat.id_categoria)} className="text-green-600 hover:underline">
                      Guardar
                    </button>
                  ) : (
                    <button
                      onClick={() => {
                        setEditandoId(cat.id_categoria);
                        setEditDescripcion(cat.descripcion);
                      }}
                      className="text-blue-600 hover:underline"
                    >
                      Editar
                    </button>
                  )}
                  <button onClick={() => handleToggle(cat.id_categoria)} className="text-amber-600 hover:underline">
                    {cat.activo ? "Desactivar" : "Activar"}
                  </button>
                  <button onClick={() => handleEliminar(cat.id_categoria)} className="text-red-600 hover:underline">
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
