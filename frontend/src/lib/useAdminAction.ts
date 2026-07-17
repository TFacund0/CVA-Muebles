"use client";

// Alias de compatibilidad: la implementación real (genérica, no específica de admin)
// vive en useApiAction.ts. Se reusa acá para no tocar los 9 componentes admin que
// ya importan useAdminAction.
export { useApiAction as useAdminAction } from "@/lib/useApiAction";
