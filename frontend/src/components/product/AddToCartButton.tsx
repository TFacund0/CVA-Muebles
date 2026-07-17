"use client";

import { useState } from "react";
import { useCart } from "@/context/CartContext";
import type { Producto } from "@/lib/api";

export default function AddToCartButton({ producto }: { producto: Producto }) {
  const { addItem } = useCart();
  const [agregado, setAgregado] = useState(false);

  function handleClick() {
    addItem({
      producto_id: producto.id_producto,
      nombre_prod: producto.nombre_prod,
      imagen: producto.imagen,
      precio_vta: producto.precio_vta,
    });
    setAgregado(true);
    setTimeout(() => setAgregado(false), 1500);
  }

  return (
    <button
      onClick={handleClick}
      className="mt-4 rounded bg-black px-4 py-2 text-white transition hover:bg-zinc-800"
    >
      {agregado ? "¡Agregado!" : "Agregar al carrito"}
    </button>
  );
}
