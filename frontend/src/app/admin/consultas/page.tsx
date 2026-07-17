import { Suspense } from "react";
import { adminFetch, type AdminConsultasResumen } from "@/lib/admin";
import ConsultasTable from "@/components/admin/ConsultasTable";
import SearchBox from "@/components/admin/SearchBox";
import Pagination from "@/components/admin/Pagination";

export default async function AdminConsultasPage({
  searchParams,
}: {
  searchParams: Promise<{ search?: string; page_consultas?: string }>;
}) {
  const { search, page_consultas } = await searchParams;

  const query = new URLSearchParams();
  if (search) query.set("search", search);
  if (page_consultas) query.set("page_consultas", page_consultas);

  const data = await adminFetch<AdminConsultasResumen>(`/consultas${query.toString() ? `?${query}` : ""}`);

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Consultas</h1>
      {!data && <p className="text-zinc-500">No se pudieron cargar las consultas.</p>}
      {data && (
        <>
          <Suspense fallback={null}>
            <SearchBox placeholder="Buscar por nombre, email o asunto..." pageParam="page_consultas" />
          </Suspense>
          <ConsultasTable consultas={data.consultas} />
          <Suspense fallback={null}>
            <Pagination pager={data.pager} pageParam="page_consultas" />
          </Suspense>
        </>
      )}
    </div>
  );
}
