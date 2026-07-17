import type { Producto } from "@/types/catalogo";

export interface Pager {
  page: number;
  per_page: number;
  total: number;
  page_count: number;
}

export interface AdminProducto extends Producto {
  stock_min: number;
  eliminado: "SI" | "NO";
}

export interface AdminProductoStats {
  productos: AdminProducto[];
  counts: { total: number; activos: number; sin_stock: number; eliminados: number };
  pager: Pager;
}

export interface AdminCategoria {
  id_categoria: number;
  descripcion: string;
  activo: number;
  total_productos: number;
  productos_activos: number;
}

export interface AdminUsuario {
  id_usuario: number;
  nombre: string;
  apellido: string;
  usuario: string;
  email: string;
  perfil_id: number;
  perfil: string;
  baja: string;
}

export interface AdminUsuarioStats {
  usuarios: AdminUsuario[];
  counts: { total: number; activos: number; admins: number; suspendidos: number };
  pager: Pager;
}

export interface AdminVenta {
  id: number;
  usuario_id: number;
  nombre: string;
  apellido: string;
  usuario: string;
  fecha: string;
  total_venta: string;
  total_pagado: number;
  estado: string;
  estado_aprobacion: string;
  tipo_pedido: string;
  prioridad: number;
}

export interface AdminVentasResumen {
  ventas: AdminVenta[];
  solicitados: AdminVenta[];
  counts: {
    total: number;
    mensuales: number;
    pendientes: number;
    en_proceso: number;
    terminados: number;
    ingresos: number;
  };
  pager: Pager;
}

export interface AdminDashboard {
  estados: { PENDIENTE: number; EN_PROCESO: number; TERMINADO: number; ENTREGADO: number };
  consultas_activas: number;
  galeria_pendientes: number;
}

export interface AdminGaleriaFoto {
  id: number;
  usuario_id: number;
  nombre: string;
  imagen: string;
  comentario: string | null;
  fecha: string;
  activo: string;
}

export interface AdminConsulta {
  id_consulta: number;
  nombre: string;
  apellido: string;
  email: string;
  telefono: string;
  asunto: string;
  descripcion: string;
  fecha: string;
  activo: string;
}

export interface AdminConsultasResumen {
  consultas: AdminConsulta[];
  counts: Record<string, number>;
  pager: Pager;
}
