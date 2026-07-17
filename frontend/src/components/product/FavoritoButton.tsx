"use client";

import { useApiAction } from "@/lib/useApiAction";
import { clientFetch } from "@/lib/clientFetch";

export default function FavoritoButton({ productoId }: { productoId: number }) {
  const { error, loading, run } = useApiAction();

  function handleClick() {
    run(() => clientFetch(`/api/favoritos/toggle/${productoId}`, { method: "POST" }));
  }

  return (
    <div>
      <button
        onClick={handleClick}
        disabled={loading}
        className="rounded border px-4 py-2 transition hover:bg-zinc-50 disabled:opacity-50"
      >
        ♥ Favorito
      </button>
      {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
    </div>
  );
}
