"use client";

import { adminMutate } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import { formatFecha } from "@/lib/format";
import type { AdminConsulta } from "@/lib/admin";

export default function ConsultasTable({ consultas }: { consultas: AdminConsulta[] }) {
  const { error, run } = useAdminAction();

  function handleArchivar(id: number) {
    run(() => adminMutate(`/consultas/${id}/eliminar`, { method: "POST" }));
  }

  function handleRestaurar(id: number) {
    run(() => adminMutate(`/consultas/${id}/restaurar`, { method: "POST" }));
  }

  function handleEliminarPermanente(id: number) {
    if (!confirm("¿Eliminar esta consulta permanentemente?")) return;
    run(() => adminMutate(`/consultas/${id}`, { method: "DELETE" }));
  }

  return (
    <div className="overflow-x-auto">
      {error && <p className="mb-3 text-sm text-red-600">{error}</p>}

      <table className="w-full text-sm">
        <thead>
          <tr className="border-b text-left text-zinc-500">
            <th className="pb-2">Nombre</th>
            <th className="pb-2">Email</th>
            <th className="pb-2">Asunto</th>
            <th className="pb-2">Fecha</th>
            <th className="pb-2">Estado</th>
            <th className="pb-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          {consultas.map((c) => (
            <tr key={c.id_consulta} className="border-b align-top">
              <td className="py-2">
                {c.nombre} {c.apellido}
              </td>
              <td className="py-2">{c.email}</td>
              <td className="py-2">{c.asunto}</td>
              <td className="py-2">{formatFecha(c.fecha)}</td>
              <td className="py-2">{c.activo === "SI" ? "Activa" : "Archivada"}</td>
              <td className="py-2">
                <div className="flex flex-wrap gap-2">
                  {c.activo === "SI" ? (
                    <button onClick={() => handleArchivar(c.id_consulta)} className="text-amber-600 hover:underline">
                      Archivar
                    </button>
                  ) : (
                    <button onClick={() => handleRestaurar(c.id_consulta)} className="text-green-600 hover:underline">
                      Restaurar
                    </button>
                  )}
                  <button onClick={() => handleEliminarPermanente(c.id_consulta)} className="text-red-600 hover:underline">
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
