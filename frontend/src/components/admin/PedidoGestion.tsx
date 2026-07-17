"use client";

import { adminMutateJson } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import { formatFecha } from "@/lib/format";
import type { VentaDetalle } from "@/lib/api";
import { useState } from "react";
import ExportarPdfButton from "@/components/shared/ExportarPdfButton";

const ESTADOS = ["PENDIENTE", "EN_PROCESO", "TERMINADO", "ENTREGADO"];

export default function PedidoGestion({ detalle }: { detalle: VentaDetalle }) {
  const { error, loading, run, setError } = useAdminAction();
  const { venta, detalles, pagos, total_pagado } = detalle;

  const [estado, setEstado] = useState(venta.estado);
  const [observaciones, setObservaciones] = useState(venta.observaciones ?? "");
  const [monto, setMonto] = useState("");
  const [nota, setNota] = useState("");

  const saldo = Number(venta.total_venta) - Number(total_pagado);

  function handleCambiarEstado() {
    run(() => adminMutateJson(`/ventas/${venta.id}/estado`, "POST", { estado }));
  }

  function handleGuardarObservaciones() {
    run(() => adminMutateJson(`/ventas/${venta.id}/observaciones`, "PUT", { observaciones }));
  }

  async function handleRegistrarPago() {
    const montoNum = Number(monto);
    if (!montoNum || montoNum <= 0) {
      setError("Ingresá un monto válido.");
      return;
    }
    const result = await run(() => adminMutateJson(`/ventas/${venta.id}/pago`, "POST", { monto: montoNum, nota }));
    if (result?.ok) {
      setMonto("");
      setNota("");
    }
  }

  return (
    <div className="max-w-3xl">
      <div id="factura-comprobante-admin" className="grid grid-cols-1 gap-8 sm:grid-cols-2">
      <div>
        <h2 className="mb-2 font-semibold">Ítems</h2>
        <ul className="text-sm">
          {detalles.map((item) => (
            <li key={item.producto_id} className="border-b py-1">
              {item.nombre_prod} x{item.cantidad} — ${item.precio}
            </li>
          ))}
        </ul>
        <p className="mt-2 font-semibold">Total: ${venta.total_venta}</p>
        <p className="text-sm text-zinc-500">Pagado: ${total_pagado}</p>
        <p className="text-sm text-zinc-500">Saldo pendiente: ${saldo.toFixed(2)}</p>

        <h2 className="mb-2 mt-6 font-semibold">Pagos registrados</h2>
        <ul className="text-sm text-zinc-600">
          {pagos.map((p) => (
            <li key={p.id}>
              {formatFecha(p.fecha)} — ${p.monto} {p.nota ? `(${p.nota})` : ""}
            </li>
          ))}
          {pagos.length === 0 && <li className="text-zinc-400">Sin pagos registrados.</li>}
        </ul>

        <div className="mt-4 flex flex-col gap-2">
          <input
            type="number"
            step="0.01"
            placeholder="Monto"
            value={monto}
            onChange={(e) => setMonto(e.target.value)}
            className="rounded border px-3 py-2"
          />
          <input
            type="text"
            placeholder="Nota (opcional)"
            value={nota}
            onChange={(e) => setNota(e.target.value)}
            className="rounded border px-3 py-2"
          />
          <button
            onClick={handleRegistrarPago}
            disabled={loading}
            className="rounded border px-3 py-2 text-sm disabled:opacity-50"
          >
            Registrar pago
          </button>
        </div>
      </div>

      <div>
        <h2 className="mb-2 font-semibold">Estado del pedido</h2>
        <div className="flex gap-2">
          <select value={estado} onChange={(e) => setEstado(e.target.value)} className="rounded border px-3 py-2">
            {ESTADOS.map((e) => (
              <option key={e} value={e}>
                {e}
              </option>
            ))}
          </select>
          <button
            onClick={handleCambiarEstado}
            disabled={loading}
            className="rounded bg-black px-3 py-2 text-sm text-white disabled:opacity-50"
          >
            Actualizar
          </button>
        </div>

        <h2 className="mb-2 mt-6 font-semibold">Observaciones</h2>
        <textarea
          value={observaciones}
          onChange={(e) => setObservaciones(e.target.value)}
          rows={5}
          className="w-full rounded border px-3 py-2"
        />
        <button
          onClick={handleGuardarObservaciones}
          disabled={loading}
          className="mt-2 rounded border px-3 py-2 text-sm disabled:opacity-50"
        >
          Guardar observaciones
        </button>

        {error && <p className="mt-3 text-sm text-red-600">{error}</p>}
      </div>
      </div>

      <ExportarPdfButton targetId="factura-comprobante-admin" filename={`pedido-${venta.id}.pdf`} />
    </div>
  );
}
