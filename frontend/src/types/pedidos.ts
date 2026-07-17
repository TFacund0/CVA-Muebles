export interface VentaResumen {
  id: number;
  fecha: string;
  total_venta: string;
  estado: string;
  estado_aprobacion: string;
  tipo_pedido: string;
  items: { producto_id: number; cantidad: number; precio: string }[];
}

export interface VentaDetalle {
  venta: VentaResumen & { usuario_id: number; observaciones: string | null };
  detalles: {
    producto_id: number;
    cantidad: number;
    precio: string;
    nombre_prod: string;
    imagen: string;
  }[];
  pagos: { id: number; monto: string; fecha: string; nota: string | null }[];
  total_pagado: string;
}
