import { Suspense } from "react";
import { adminFetch, type AdminUsuarioStats } from "@/lib/admin";
import UsuariosTable from "@/components/admin/UsuariosTable";
import SearchBox from "@/components/admin/SearchBox";
import Pagination from "@/components/admin/Pagination";

export default async function AdminUsuariosPage({
  searchParams,
}: {
  searchParams: Promise<{ search?: string; page_usuarios?: string }>;
}) {
  const { search, page_usuarios } = await searchParams;

  const query = new URLSearchParams();
  if (search) query.set("search", search);
  if (page_usuarios) query.set("page_usuarios", page_usuarios);

  const data = await adminFetch<AdminUsuarioStats>(`/usuarios${query.toString() ? `?${query}` : ""}`);

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
          <Suspense fallback={null}>
            <SearchBox placeholder="Buscar por nombre, usuario o email..." pageParam="page_usuarios" />
          </Suspense>
          <UsuariosTable usuarios={data.usuarios} />
          <Suspense fallback={null}>
            <Pagination pager={data.pager} pageParam="page_usuarios" />
          </Suspense>
        </>
      )}
    </div>
  );
}
