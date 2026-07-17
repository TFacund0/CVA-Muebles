import ProductoForm from "@/components/admin/ProductoForm";
import { getCategorias } from "@/lib/api";

export default async function NuevoProductoPage() {
  const categorias = await getCategorias();

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Nuevo producto</h1>
      <ProductoForm categorias={categorias} />
    </div>
  );
}
