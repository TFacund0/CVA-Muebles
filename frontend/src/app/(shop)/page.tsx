import Link from "next/link";
import ProductoCard from "@/components/product/ProductoCard";
import { getCategorias, getProductos, type Categoria, type Producto } from "@/lib/api";

export default async function Home({
  searchParams,
}: {
  searchParams: Promise<{ categoria?: string }>;
}) {
  const { categoria } = await searchParams;

  let productos: Producto[] = [];
  let categorias: Categoria[] = [];
  let error: string | null = null;

  try {
    [productos, categorias] = await Promise.all([getProductos(categoria), getCategorias()]);
  } catch (err) {
    error = err instanceof Error ? err.message : "No se pudo cargar el catálogo.";
  }

  return (
    <main className="mx-auto max-w-5xl px-6 py-12">
      <h1 className="mb-8 text-3xl font-semibold">Catálogo CVA Muebles</h1>

      {categorias.length > 0 && (
        <div className="mb-6 flex flex-wrap gap-2">
          <Link
            href="/"
            className={`rounded-full border px-3 py-1 text-sm ${!categoria ? "bg-black text-white" : ""}`}
          >
            Todos
          </Link>
          {categorias.map((cat) => (
            <Link
              key={cat.id_categoria}
              href={`/?categoria=${encodeURIComponent(cat.descripcion)}`}
              className={`rounded-full border px-3 py-1 text-sm ${
                categoria === cat.descripcion ? "bg-black text-white" : ""
              }`}
            >
              {cat.descripcion}
            </Link>
          ))}
        </div>
      )}

      {error && <p className="text-red-600">{error}</p>}

      {!error && productos.length === 0 && (
        <p className="text-zinc-500">No hay productos disponibles por el momento.</p>
      )}

      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        {productos.map((producto) => (
          <ProductoCard key={producto.id_producto} producto={producto} />
        ))}
      </div>
    </main>
  );
}
