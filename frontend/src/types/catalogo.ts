export interface ImagenGaleria {
  id: number;
  producto_id: number;
  imagen: string;
  orden: number;
}

export interface Producto {
  id_producto: number;
  nombre_prod: string;
  imagen: string;
  categoria_id: number;
  categoria: string;
  precio: string;
  precio_vta: string;
  stock: number;
  descripcion: string | null;
  galeria?: ImagenGaleria[];
}

export interface Categoria {
  id_categoria: number;
  descripcion: string;
  activo: number;
}
