import { notFound, redirect } from "next/navigation";
import ProductoForm from "@/components/admin/ProductoForm";
import GaleriaProductoAdmin from "@/components/admin/GaleriaProductoAdmin";
import { getCategorias, getProducto } from "@/lib/api";
import { adminFetch, type AdminProductoStats } from "@/lib/admin";

export default async function EditarProductoPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  // El endpoint público /productos/{id} oculta los archivados; para edición admin
  // se busca en el listado admin completo, que sí incluye archivados.
  const [categorias, listado, productoConGaleria] = await Promise.all([
    getCategorias(),
    adminFetch<AdminProductoStats>("/productos"),
    getProducto(id).catch(() => null),
  ]);

  if (listado === null) {
    redirect("/login");
  }

  const producto = listado.productos.find((p) => p.id_producto === Number(id));
  if (!producto) {
    notFound();
  }

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Editar producto</h1>
      <ProductoForm categorias={categorias} producto={producto} />

      <hr className="my-8 max-w-lg" />

      <h2 className="mb-4 text-xl font-semibold">Galería de fotos secundarias</h2>
      <GaleriaProductoAdmin productoId={producto.id_producto} galeria={productoConGaleria?.galeria ?? []} />
    </div>
  );
}
