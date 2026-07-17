"use client";

import { adminMutate, adminMutateJson } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import type { AdminUsuario } from "@/lib/admin";

export default function UsuariosTable({ usuarios }: { usuarios: AdminUsuario[] }) {
  const { error, run } = useAdminAction();

  function handleEstado(id: number, accion: "baja" | "activar") {
    run(() => adminMutateJson(`/usuarios/${id}/estado`, "POST", { accion }));
  }

  function handleCambiarPerfil(id: number) {
    run(() => adminMutate(`/usuarios/${id}/perfil`, { method: "POST" }));
  }

  function handleEliminar(id: number) {
    if (!confirm("¿Eliminar permanentemente este usuario? Solo es posible si no tiene compras asociadas.")) return;
    run(() => adminMutate(`/usuarios/${id}`, { method: "DELETE" }));
  }

  return (
    <div>
      {error && <p className="mb-3 text-sm text-red-600">{error}</p>}

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b text-left text-zinc-500">
              <th className="pb-2">Nombre</th>
              <th className="pb-2">Usuario</th>
              <th className="pb-2">Email</th>
              <th className="pb-2">Perfil</th>
              <th className="pb-2">Estado</th>
              <th className="pb-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            {usuarios.map((u) => (
              <tr key={u.id_usuario} className="border-b">
                <td className="py-2">
                  {u.nombre} {u.apellido}
                </td>
                <td className="py-2">{u.usuario}</td>
                <td className="py-2">{u.email}</td>
                <td className="py-2">{u.perfil}</td>
                <td className="py-2">{u.baja === "SI" ? "Suspendido" : "Activo"}</td>
                <td className="py-2">
                  <div className="flex flex-wrap gap-2">
                    <button onClick={() => handleCambiarPerfil(u.id_usuario)} className="text-blue-600 hover:underline">
                      {u.perfil_id === 1 ? "Quitar admin" : "Hacer admin"}
                    </button>
                    {u.baja === "SI" ? (
                      <button onClick={() => handleEstado(u.id_usuario, "activar")} className="text-green-600 hover:underline">
                        Reactivar
                      </button>
                    ) : (
                      <button onClick={() => handleEstado(u.id_usuario, "baja")} className="text-amber-600 hover:underline">
                        Suspender
                      </button>
                    )}
                    <button onClick={() => handleEliminar(u.id_usuario)} className="text-red-600 hover:underline">
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {usuarios.length === 0 && (
              <tr>
                <td colSpan={6} className="py-6 text-center text-zinc-400">
                  No se encontraron usuarios.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
