import { apiFetchAuthed } from "@/lib/auth";

// Los tipos viven en @/types/admin (contratos reusables sin acoplar al código de fetch);
// se re-exportan acá para no romper los imports existentes de `@/lib/admin`.
export type {
  AdminProducto,
  AdminProductoStats,
  AdminCategoria,
  AdminUsuario,
  AdminUsuarioStats,
  AdminVenta,
  AdminVentasResumen,
  AdminDashboard,
  AdminGaleriaFoto,
  AdminConsulta,
  AdminConsultasResumen,
} from "@/types/admin";

/** Fetch server-side para páginas del panel admin. Redirige el caller si retorna null (no autenticado/no admin). */
export function adminFetch<T>(path: string): Promise<T | null> {
  return apiFetchAuthed<T>(`/admin${path}`);
}
