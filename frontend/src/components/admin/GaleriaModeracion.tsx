"use client";

import Image from "next/image";
import { adminMutate } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import type { AdminGaleriaFoto } from "@/lib/admin";

export default function GaleriaModeracion({ fotos }: { fotos: AdminGaleriaFoto[] }) {
  const { error, run } = useAdminAction();

  function handleAprobar(id: number) {
    run(() => adminMutate(`/galeria/${id}/aprobar`, { method: "POST" }));
  }

  function handleEliminar(id: number) {
    if (!confirm("¿Eliminar esta foto?")) return;
    run(() => adminMutate(`/galeria/${id}`, { method: "DELETE" }));
  }

  return (
    <div>
      {error && <p className="mb-3 text-sm text-red-600">{error}</p>}

      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        {fotos.map((foto) => (
          <div key={foto.id} className="rounded-lg border p-2">
            <div className="relative aspect-square overflow-hidden rounded bg-zinc-100">
              <Image src={foto.imagen} alt={foto.comentario ?? ""} fill sizes="200px" className="object-cover" />
            </div>
            <p className="mt-2 text-xs text-zinc-500">Por: {foto.nombre}</p>
            {foto.comentario && <p className="text-xs text-zinc-600">{foto.comentario}</p>}
            <p className="text-xs font-semibold">{foto.activo === "SI" ? "Publicada" : "Pendiente"}</p>
            <div className="mt-2 flex gap-2 text-sm">
              {foto.activo !== "SI" && (
                <button onClick={() => handleAprobar(foto.id)} className="text-green-600 hover:underline">
                  Aprobar
                </button>
              )}
              <button onClick={() => handleEliminar(foto.id)} className="text-red-600 hover:underline">
                Eliminar
              </button>
            </div>
          </div>
        ))}
        {fotos.length === 0 && <p className="col-span-full text-zinc-500">No hay fotos.</p>}
      </div>
    </div>
  );
}
