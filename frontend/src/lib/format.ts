export const ESTADO_LABEL: Record<string, string> = {
  PENDIENTE: "Pendiente",
  EN_PROCESO: "En Proceso",
  TERMINADO: "Terminado",
  ENTREGADO: "Entregado",
};

export function formatEstado(estado: string): string {
  return ESTADO_LABEL[estado] ?? estado;
}

export function formatFecha(fecha: string): string {
  return new Date(fecha).toLocaleDateString("es-AR");
}

export function formatMoneda(valor: number | string): string {
  return `$${Number(valor).toFixed(2)}`;
}
