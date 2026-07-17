"use client";

import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { useApiAction } from "@/lib/useApiAction";
import { clientFetch } from "@/lib/clientFetch";

export default function SubirFotoPage() {
  const router = useRouter();
  const { error, loading, run, setError } = useApiAction();
  const [comentario, setComentario] = useState("");
  const [archivo, setArchivo] = useState<File | null>(null);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();

    if (!archivo) {
      setError("Seleccioná una imagen.");
      return;
    }

    const formData = new FormData();
    formData.append("imagen", archivo);
    formData.append("comentario", comentario);

    const result = await run(() => clientFetch("/api/galeria", { method: "POST", body: formData }));
    if (result?.ok) router.push("/galeria");
  }

  return (
    <main className="mx-auto max-w-md px-6 py-12">
      <h1 className="mb-6 text-2xl font-semibold">Subir foto a la galería</h1>

      <form onSubmit={handleSubmit} className="flex flex-col gap-4">
        <input
          type="file"
          accept="image/jpeg,image/png,image/webp"
          onChange={(e) => setArchivo(e.target.files?.[0] ?? null)}
          required
        />
        <textarea
          placeholder="Comentario (opcional)"
          value={comentario}
          onChange={(e) => setComentario(e.target.value)}
          maxLength={255}
          rows={3}
          className="rounded border px-3 py-2"
        />
        {error && <p className="text-sm text-red-600">{error}</p>}
        <button
          type="submit"
          disabled={loading}
          className="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
        >
          {loading ? "Subiendo..." : "Enviar para moderación"}
        </button>
        <p className="text-xs text-zinc-500">
          Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo 2MB. Tu foto será revisada antes de publicarse.
        </p>
      </form>
    </main>
  );
}
