export interface Favorito {
  id: number;
  producto_id: number;
  nombre_prod: string;
  imagen: string;
  precio_vta: string;
  descripcion: string | null;
  categoria: string;
}

export interface FotoGaleria {
  id: number;
  usuario_id: number;
  imagen: string;
  comentario: string | null;
  fecha: string;
}
