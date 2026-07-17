"use client";

import Image from "next/image";
import { useState } from "react";
import { adminMutate } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import type { ImagenGaleria } from "@/lib/api";

export default function GaleriaProductoAdmin({
  productoId,
  galeria,
}: {
  productoId: number;
  galeria: ImagenGaleria[];
}) {
  const { error, loading, run } = useAdminAction();
  const [archivos, setArchivos] = useState<FileList | null>(null);

  async function handleUpload() {
    if (!archivos || archivos.length === 0) return;

    const formData = new FormData();
    Array.from(archivos).forEach((file) => formData.append("imagenes[]", file));

    const result = await run(() =>
      adminMutate(`/productos/${productoId}/galeria`, { method: "POST", body: formData })
    );

    if (result?.ok) setArchivos(null);
  }

  function handleDelete(fotoId: number) {
    run(() => adminMutate(`/productos/galeria/${fotoId}`, { method: "DELETE" }));
  }

  return (
    <div className="max-w-lg">
      <div className="mb-4 grid grid-cols-4 gap-2">
        {galeria.map((foto) => (
          <div key={foto.id} className="group relative aspect-square overflow-hidden rounded bg-zinc-100">
            <Image src={foto.imagen} alt="" fill sizes="150px" className="object-cover" />
            <button
              onClick={() => handleDelete(foto.id)}
              className="absolute right-1 top-1 rounded bg-black/70 px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
            >
              Eliminar
            </button>
          </div>
        ))}
        {galeria.length === 0 && <p className="col-span-4 text-sm text-zinc-500">Sin fotos secundarias.</p>}
      </div>

      <input
        type="file"
        multiple
        accept="image/jpeg,image/png,image/webp"
        onChange={(e) => setArchivos(e.target.files)}
      />
      <button
        onClick={handleUpload}
        disabled={loading || !archivos}
        className="ml-3 rounded border px-3 py-1 text-sm disabled:opacity-50"
      >
        {loading ? "Subiendo..." : "Subir fotos"}
      </button>

      {error && <p className="mt-2 text-sm text-red-600">{error}</p>}
    </div>
  );
}
