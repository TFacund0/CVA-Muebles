import { Suspense } from "react";
import Link from "next/link";
import Hero from "@/components/home/Hero";
import ProductoCard from "@/components/product/ProductoCard";
import SearchBar from "@/components/product/SearchBar";
import { getCategorias, getProductos, type Categoria, type Producto } from "@/lib/api";

export default async function Home({
  searchParams,
}: {
  searchParams: Promise<{ categoria?: string; q?: string }>;
}) {
  const { categoria, q } = await searchParams;

  let productos: Producto[] = [];
  let categorias: Categoria[] = [];
  let error: string | null = null;

  try {
    [productos, categorias] = await Promise.all([getProductos(categoria), getCategorias()]);
  } catch (err) {
    error = err instanceof Error ? err.message : "No se pudo cargar el catálogo.";
  }

  const filtrados = q
    ? productos.filter((p) => p.nombre_prod.toLowerCase().includes(q.toLowerCase()))
    : productos;

  return (
    <main>
      <Hero />

      <div className="mx-auto max-w-6xl px-6 py-12">
      <h1 className="font-heading mb-8 text-4xl font-bold text-cva-brown">Catálogo CVA Muebles</h1>

      <div className="mb-6 grid gap-3 lg:grid-cols-[minmax(0,320px)_1fr] lg:items-center">
        <Suspense fallback={<div className="search-artisan w-full" />}>
          <SearchBar />
        </Suspense>

        {categorias.length > 0 && (
          <div className="flex flex-wrap gap-2 lg:justify-end">
            <Link
              href="/"
              className={`btn-filter-artisan ${!categoria ? "active" : ""}`}
            >
              Todos
            </Link>
            {categorias.map((cat) => (
              <Link
                key={cat.id_categoria}
                href={`/?categoria=${encodeURIComponent(cat.descripcion)}`}
                className={`btn-filter-artisan ${categoria === cat.descripcion ? "active" : ""}`}
              >
                {cat.descripcion}
              </Link>
            ))}
          </div>
        )}
      </div>

      {error && <p className="text-red-600">{error}</p>}

      {!error && filtrados.length === 0 && (
        <p className="text-cva-text-muted">No hay productos disponibles por el momento.</p>
      )}

      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        {filtrados.map((producto, index) => (
          <ProductoCard key={producto.id_producto} producto={producto} priority={index < 3} />
        ))}
      </div>
      </div>
    </main>
  );
}
