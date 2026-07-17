import { adminFetch, type AdminDashboard } from "@/lib/admin";

export default async function AdminDashboardPage() {
  const stats = await adminFetch<AdminDashboard>("/dashboard");

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Dashboard</h1>

      {!stats && <p className="text-zinc-500">No se pudieron cargar las estadísticas.</p>}

      {stats && (
        <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
          <div className="rounded-lg border p-4">
            <p className="text-sm text-zinc-500">Pendientes</p>
            <p className="text-2xl font-semibold">{stats.estados.PENDIENTE}</p>
          </div>
          <div className="rounded-lg border p-4">
            <p className="text-sm text-zinc-500">En Proceso</p>
            <p className="text-2xl font-semibold">{stats.estados.EN_PROCESO}</p>
          </div>
          <div className="rounded-lg border p-4">
            <p className="text-sm text-zinc-500">Terminados</p>
            <p className="text-2xl font-semibold">{stats.estados.TERMINADO}</p>
          </div>
          <div className="rounded-lg border p-4">
            <p className="text-sm text-zinc-500">Entregados</p>
            <p className="text-2xl font-semibold">{stats.estados.ENTREGADO}</p>
          </div>
          <div className="rounded-lg border p-4">
            <p className="text-sm text-zinc-500">Consultas activas</p>
            <p className="text-2xl font-semibold">{stats.consultas_activas}</p>
          </div>
          <div className="rounded-lg border p-4">
            <p className="text-sm text-zinc-500">Fotos pendientes de moderar</p>
            <p className="text-2xl font-semibold">{stats.galeria_pendientes}</p>
          </div>
        </div>
      )}
    </div>
  );
}
