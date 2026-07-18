"use client";

import { useApiAction } from "@/lib/useApiAction";
import { clientFetch } from "@/lib/clientFetch";

export default function FavoritoButton({
  productoId,
  circular = false,
}: {
  productoId: number;
  circular?: boolean;
}) {
  const { error, loading, run } = useApiAction();

  function handleClick() {
    run(() => clientFetch(`/api/favoritos/toggle/${productoId}`, { method: "POST" }));
  }

  if (circular) {
    return (
      <button
        onClick={handleClick}
        disabled={loading}
        aria-label="Favorito"
        className="btn-fav-artisan disabled:opacity-50"
      >
        ♥
      </button>
    );
  }

  return (
    <div>
      <button onClick={handleClick} disabled={loading} className="btn-outline-brown disabled:opacity-50">
        ♥ Favorito
      </button>
      {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
    </div>
  );
}
