import { adminFetch, type AdminGaleriaFoto } from "@/lib/admin";
import GaleriaModeracion from "@/components/admin/GaleriaModeracion";

export default async function AdminGaleriaPage() {
  const fotos = await adminFetch<AdminGaleriaFoto[]>("/galeria");

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Moderación de Galería</h1>
      {!fotos && <p className="text-zinc-500">No se pudieron cargar las fotos.</p>}
      {fotos && <GaleriaModeracion fotos={fotos} />}
    </div>
  );
}
