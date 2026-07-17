import Image from "next/image";
import Link from "next/link";
import { getGaleria, type FotoGaleria } from "@/lib/api";

export default async function GaleriaPage() {
  let fotos: FotoGaleria[] = [];
  let error: string | null = null;

  try {
    fotos = await getGaleria();
  } catch (err) {
    error = err instanceof Error ? err.message : "No se pudo cargar la galería.";
  }

  return (
    <main className="mx-auto max-w-5xl px-6 py-12">
      <div className="mb-8 flex items-center justify-between">
        <h1 className="text-3xl font-semibold">Galería de Clientes</h1>
        <Link href="/galeria/subir" className="rounded bg-black px-4 py-2 text-sm text-white">
          Subir mi foto
        </Link>
      </div>

      {error && <p className="text-red-600">{error}</p>}

      {!error && fotos.length === 0 && (
        <p className="text-zinc-500">Todavía no hay fotos publicadas.</p>
      )}

      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        {fotos.map((foto) => (
          <div key={foto.id} className="relative aspect-square overflow-hidden rounded-lg bg-zinc-100">
            <Image src={foto.imagen} alt={foto.comentario ?? "Foto de cliente"} fill sizes="300px" className="object-cover" />
          </div>
        ))}
      </div>
    </main>
  );
}
