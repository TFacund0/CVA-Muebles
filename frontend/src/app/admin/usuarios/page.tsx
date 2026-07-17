import { adminFetch, type AdminUsuarioStats } from "@/lib/admin";
import UsuariosTable from "@/components/admin/UsuariosTable";

export default async function AdminUsuariosPage() {
  const data = await adminFetch<AdminUsuarioStats>("/usuarios");

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Usuarios</h1>

      {!data && <p className="text-zinc-500">No se pudieron cargar los usuarios.</p>}

      {data && (
        <>
          <div className="mb-4 flex gap-4 text-sm text-zinc-600">
            <span>Total: {data.counts.total}</span>
            <span>Activos: {data.counts.activos}</span>
            <span>Admins: {data.counts.admins}</span>
            <span>Suspendidos: {data.counts.suspendidos}</span>
          </div>
          <UsuariosTable usuarios={data.usuarios} />
        </>
      )}
    </div>
  );
}
