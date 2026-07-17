"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import type { ClientFetchResult } from "@/lib/clientFetch";

/**
 * Centraliza el patrón repetido en cualquier acción autenticada desde el cliente
 * (toggle favorito, subir foto, guardar perfil, checkout, CRUDs admin, etc.):
 * redirigir a /login si el JWT expiró, mostrar el mensaje de error, y refrescar
 * la página server-rendered tras un éxito. Antes esto vivía copiado a mano en
 * 14 archivos (9 del panel admin + 5 del lado público).
 */
export function useApiAction() {
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function run<T>(action: () => Promise<ClientFetchResult<T>>): Promise<ClientFetchResult<T> | null> {
    setError(null);
    setLoading(true);
    const result = await action();
    setLoading(false);

    if (result.unauthorized) {
      router.push("/login");
      return null;
    }

    if (!result.ok) {
      setError(result.message);
      return result;
    }

    router.refresh();
    return result;
  }

  return { error, setError, loading, run };
}
