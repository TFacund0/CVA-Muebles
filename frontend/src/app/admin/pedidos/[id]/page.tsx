import { notFound, redirect } from "next/navigation";
import { apiFetchAuthed } from "@/lib/auth";
import type { VentaDetalle } from "@/lib/api";
import PedidoGestion from "@/components/admin/PedidoGestion";

export default async function AdminPedidoDetallePage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  let detalle: VentaDetalle | null;
  try {
    // Reutiliza GET /api/v1/ventas/{id} (Fase A) — ya permite acceso admin.
    detalle = await apiFetchAuthed<VentaDetalle>(`/ventas/${id}`);
  } catch {
    notFound();
  }

  if (detalle === null) {
    redirect("/login");
  }

  return (
    <div>
      <h1 className="mb-6 text-2xl font-semibold">Gestión de Pedido #{detalle.venta.id}</h1>
      <PedidoGestion detalle={detalle} />
    </div>
  );
}
