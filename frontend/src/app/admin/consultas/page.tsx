import { adminFetch, type AdminConsultasResumen } from "@/lib/admin";
import ConsultasTable from "@/components/admin/ConsultasTable";

export default async function AdminConsultasPage() {
  const data = await adminFetch<AdminConsultasResumen>("/consultas");

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Consultas</h1>
      {!data && <p className="text-zinc-500">No se pudieron cargar las consultas.</p>}
      {data && <ConsultasTable consultas={data.consultas} />}
    </div>
  );
}
