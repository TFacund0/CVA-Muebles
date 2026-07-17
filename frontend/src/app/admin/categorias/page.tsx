import { adminFetch, type AdminCategoria } from "@/lib/admin";
import CategoriasTable from "@/components/admin/CategoriasTable";

export default async function AdminCategoriasPage() {
  const categorias = await adminFetch<AdminCategoria[]>("/categorias");

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Categorías</h1>
      {!categorias && <p className="text-zinc-500">No se pudieron cargar las categorías.</p>}
      {categorias && <CategoriasTable categorias={categorias} />}
    </div>
  );
}
