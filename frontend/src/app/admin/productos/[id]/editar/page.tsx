import { notFound, redirect } from "next/navigation";
import ProductoForm from "@/components/admin/ProductoForm";
import GaleriaProductoAdmin from "@/components/admin/GaleriaProductoAdmin";
import { getCategorias } from "@/lib/api";
import { adminFetch, type AdminProducto } from "@/lib/admin";
import type { ImagenGaleria } from "@/lib/api";

type AdminProductoConGaleria = AdminProducto & { galeria?: ImagenGaleria[] };

export default async function EditarProductoPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  let producto: AdminProductoConGaleria | null;
  let categorias;
  try {
    [categorias, producto] = await Promise.all([
      getCategorias(),
      adminFetch<AdminProductoConGaleria>(`/productos/${id}`),
    ]);
  } catch {
    notFound();
  }

  if (producto === null) {
    redirect("/login");
  }

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Editar producto</h1>
      <ProductoForm categorias={categorias} producto={producto} />

      <hr className="my-8 max-w-lg" />

      <h2 className="mb-4 text-xl font-semibold">Galería de fotos secundarias</h2>
      <GaleriaProductoAdmin productoId={producto.id_producto} galeria={producto.galeria ?? []} />
    </div>
  );
}
