"use client";

import Link from "next/link";
import { adminMutateJson } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import { formatFecha } from "@/lib/format";
import type { AdminVenta } from "@/lib/admin";

export default function PedidosTable({
  pedidos,
  showPrioridad,
}: {
  pedidos: AdminVenta[];
  showPrioridad: boolean;
}) {
  const { error, run } = useAdminAction();

  function handlePrioridad(id: number, direccion: "subir" | "bajar") {
    run(() => adminMutateJson(`/ventas/${id}/prioridad`, "POST", { direccion }));
  }

  function handleAprobar(id: number, estado: "ACEPTADO" | "RECHAZADO") {
    run(() => adminMutateJson(`/ventas/${id}/estado`, "POST", { estado }));
  }

  return (
    <div className="mb-4 overflow-x-auto">
      {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
      <table className="w-full text-sm">
        <thead>
          <tr className="border-b text-left text-zinc-500">
            <th className="pb-2">#</th>
            <th className="pb-2">Cliente</th>
            <th className="pb-2">Fecha</th>
            <th className="pb-2 text-right">Total</th>
            <th className="pb-2">Estado</th>
            <th className="pb-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          {pedidos.map((v) => (
            <tr key={v.id} className="border-b">
              <td className="py-2">{v.id}</td>
              <td className="py-2">
                {v.nombre} {v.apellido}
              </td>
              <td className="py-2">{formatFecha(v.fecha)}</td>
              <td className="py-2 text-right">${v.total_venta}</td>
              <td className="py-2">{v.estado}</td>
              <td className="py-2">
                <div className="flex flex-wrap items-center gap-2">
                  <Link href={`/admin/pedidos/${v.id}`} className="text-blue-600 hover:underline">
                    Gestionar
                  </Link>
                  {v.estado_aprobacion === "SOLICITUD" && (
                    <>
                      <button onClick={() => handleAprobar(v.id, "ACEPTADO")} className="text-green-600 hover:underline">
                        Aceptar
                      </button>
                      <button onClick={() => handleAprobar(v.id, "RECHAZADO")} className="text-red-600 hover:underline">
                        Rechazar
                      </button>
                    </>
                  )}
                  {showPrioridad && (
                    <>
                      <button onClick={() => handlePrioridad(v.id, "subir")} className="text-zinc-600 hover:underline">
                        ↑
                      </button>
                      <button onClick={() => handlePrioridad(v.id, "bajar")} className="text-zinc-600 hover:underline">
                        ↓
                      </button>
                    </>
                  )}
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
