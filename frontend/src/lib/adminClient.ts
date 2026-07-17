"use client";

import { clientFetch, clientFetchJson, type ClientFetchResult } from "@/lib/clientFetch";

/**
 * Especialización de clientFetch para el proxy admin (/api/admin/[...path]).
 * Mutaciones admin desde Client Components — nunca directo al backend, para no
 * exponer el JWT al navegador.
 */
export type AdminMutateResult<T = unknown> = ClientFetchResult<T>;

export function adminMutate<T = unknown>(path: string, init?: RequestInit): Promise<AdminMutateResult<T>> {
  return clientFetch<T>(`/api/admin${path}`, init);
}

export function adminMutateJson<T = unknown>(
  path: string,
  method: string,
  payload: unknown
): Promise<AdminMutateResult<T>> {
  return clientFetchJson<T>(`/api/admin${path}`, method, payload);
}
