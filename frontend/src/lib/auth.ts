import { cookies } from "next/headers";

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8080/api/v1";

export async function getAccessToken(): Promise<string | null> {
  const store = await cookies();
  return store.get("cva_access_token")?.value ?? null;
}

/**
 * Fetch autenticado desde un Server Component: adjunta el JWT leído de la cookie httpOnly.
 * Si no hay token, devuelve null en vez de pegarle a la API (el caller decide si redirige a /login).
 */
export async function apiFetchAuthed<T>(path: string, init?: RequestInit): Promise<T | null> {
  const token = await getAccessToken();
  if (!token) return null;

  const res = await fetch(`${API_URL}${path}`, {
    ...init,
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
      ...init?.headers,
    },
    cache: "no-store",
  });

  const body = await res.json();
  if (!res.ok || body.status === "error") {
    if (res.status === 401) return null;
    throw new Error(body.message ?? `Error ${res.status} al consultar la API`);
  }

  return body.data as T;
}
