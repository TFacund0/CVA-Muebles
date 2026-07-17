"use client";

/**
 * Helper genérico para llamadas fetch desde Client Components hacia los
 * Route Handlers propios de Next (nunca directo al backend PHP con JWT expuesto).
 * Normaliza el envelope de la API ({status, data, message}) a un resultado tipado
 * único, reusado tanto por el panel admin (adminClient.ts) como por las acciones
 * públicas autenticadas (favoritos, perfil, carrito, galería).
 */
export interface ClientFetchResult<T = unknown> {
  ok: boolean;
  unauthorized: boolean;
  message: string | null;
  data: T | null;
}

export async function clientFetch<T = unknown>(url: string, init?: RequestInit): Promise<ClientFetchResult<T>> {
  const res = await fetch(url, init);

  if (res.status === 401 || res.status === 403) {
    return { ok: false, unauthorized: true, message: "No autorizado.", data: null };
  }

  const body = await res.json().catch(() => null);

  if (!res.ok || body?.status === "error") {
    return { ok: false, unauthorized: false, message: body?.message ?? "Ocurrió un error.", data: null };
  }

  return { ok: true, unauthorized: false, message: null, data: (body?.data ?? null) as T | null };
}

export function clientFetchJson<T = unknown>(
  url: string,
  method: string,
  payload: unknown
): Promise<ClientFetchResult<T>> {
  return clientFetch<T>(url, {
    method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
}
