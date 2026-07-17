import Link from "next/link";
import { notFound, redirect } from "next/navigation";
import { apiFetchAuthed } from "@/lib/auth";
import { formatEstado, formatFecha } from "@/lib/format";
import type { VentaDetalle } from "@/lib/api";
import ExportarPdfButton from "@/components/shared/ExportarPdfButton";

export default async function PedidoDetallePage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  let detalle: VentaDetalle | null;
  try {
    detalle = await apiFetchAuthed<VentaDetalle>(`/ventas/${id}`);
  } catch {
    notFound();
  }

  if (detalle === null) {
    redirect("/login");
  }

  const { venta, detalles, pagos, total_pagado } = detalle;
  const saldo = Number(venta.total_venta) - Number(total_pagado);

  return (
    <main className="mx-auto max-w-2xl px-6 py-12">
      <Link href="/pedidos" className="text-sm underline">
        &larr; Volver a mis pedidos
      </Link>

      <div id="factura-comprobante" className="mt-4 rounded-lg border p-6">
        <div className="mb-4 flex items-center justify-between">
          <h1 className="text-2xl font-semibold">Pedido #{venta.id}</h1>
          <span className="rounded-full border px-3 py-1 text-sm">{formatEstado(venta.estado)}</span>
        </div>

        <p className="text-sm text-zinc-500">Fecha: {formatFecha(venta.fecha)}</p>

        <table className="mt-6 w-full text-sm">
          <thead>
            <tr className="border-b text-left text-zinc-500">
              <th className="pb-2">Producto</th>
              <th className="pb-2 text-center">Cant.</th>
              <th className="pb-2 text-right">Precio</th>
              <th className="pb-2 text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            {detalles.map((item) => (
              <tr key={item.producto_id} className="border-b">
                <td className="py-2">{item.nombre_prod}</td>
                <td className="py-2 text-center">{item.cantidad}</td>
                <td className="py-2 text-right">${item.precio}</td>
                <td className="py-2 text-right">${(Number(item.precio) * item.cantidad).toFixed(2)}</td>
              </tr>
            ))}
          </tbody>
        </table>

        <div className="mt-4 flex flex-col items-end gap-1 text-sm">
          <p className="text-lg font-semibold">Total: ${venta.total_venta}</p>
          <p className="text-zinc-500">Pagado: ${total_pagado}</p>
          <p className="text-zinc-500">Saldo pendiente: ${saldo.toFixed(2)}</p>
        </div>

        {venta.observaciones && (
          <p className="mt-4 text-sm text-zinc-600">Observaciones: {venta.observaciones}</p>
        )}

        {pagos.length > 0 && (
          <div className="mt-6">
            <h2 className="mb-2 font-medium">Pagos registrados</h2>
            <ul className="text-sm text-zinc-600">
              {pagos.map((pago) => (
                <li key={pago.id}>
                  {formatFecha(pago.fecha)} — ${pago.monto}
                  {pago.nota ? ` (${pago.nota})` : ""}
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      <ExportarPdfButton targetId="factura-comprobante" filename={`pedido-${venta.id}.pdf`} />
    </main>
  );
}
