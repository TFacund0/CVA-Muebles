import type { ApiEnvelope } from "@/types/api";
import type { Categoria, Producto } from "@/types/catalogo";
import type { LoginResponse } from "@/types/usuario";
import type { FotoGaleria } from "@/types/social";

// Los tipos de datos viven en @/types/* (contratos reusables sin acoplar al código de fetch);
// se re-exportan acá para no romper los imports existentes de `@/lib/api`.
export type { ApiEnvelope } from "@/types/api";
export type { ImagenGaleria, Producto, Categoria } from "@/types/catalogo";
export type { Perfil, LoginResponse } from "@/types/usuario";
export type { Favorito, FotoGaleria } from "@/types/social";
export type { VentaResumen, VentaDetalle } from "@/types/pedidos";

export const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8080/api/v1";

async function apiFetch<T>(path: string, init?: RequestInit): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    ...init,
    headers: {
      "Content-Type": "application/json",
      ...init?.headers,
    },
  });

  const body: ApiEnvelope<T> = await res.json();

  if (!res.ok || body.status === "error") {
    throw new Error(body.message ?? `Error ${res.status} al consultar la API`);
  }

  return body.data as T;
}

export function getProductos(categoria?: string): Promise<Producto[]> {
  const query = categoria ? `?categoria=${encodeURIComponent(categoria)}` : "";
  return apiFetch<Producto[]>(`/productos${query}`);
}

export function getProducto(id: number | string): Promise<Producto> {
  return apiFetch<Producto>(`/productos/${id}`);
}

export function getCategorias(): Promise<Categoria[]> {
  return apiFetch<Categoria[]>("/categorias");
}

export function login(loginValue: string, password: string): Promise<LoginResponse> {
  return apiFetch<LoginResponse>("/auth/login", {
    method: "POST",
    body: JSON.stringify({ login: loginValue, password }),
  });
}

export interface RegisterData {
  name: string;
  surname: string;
  user: string;
  email: string;
  pass: string;
}

export function register(data: RegisterData): Promise<LoginResponse> {
  return apiFetch<LoginResponse>("/auth/register", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export function getGaleria(): Promise<FotoGaleria[]> {
  return apiFetch<FotoGaleria[]>("/galeria");
}

export function enviarConsulta(data: {
  nombre: string;
  apellido: string;
  email: string;
  telefono: string;
  asunto: string;
  descripcion: string;
}): Promise<null> {
  return apiFetch<null>("/consultas", {
    method: "POST",
    body: JSON.stringify(data),
  });
}
