"use client";

import { createContext, useContext, useEffect, useState, type ReactNode } from "react";

export interface CartItem {
  producto_id: number;
  nombre_prod: string;
  imagen: string;
  precio_vta: string;
  cantidad: number;
}

interface CartContextValue {
  items: CartItem[];
  totalItems: number;
  totalPrecio: number;
  addItem: (item: Omit<CartItem, "cantidad">, cantidad?: number) => void;
  removeItem: (producto_id: number) => void;
  setCantidad: (producto_id: number, cantidad: number) => void;
  clear: () => void;
}

const CartContext = createContext<CartContextValue | null>(null);

const STORAGE_KEY = "cva_cart";

export function CartProvider({ children }: { children: ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([]);
  const [hydrated, setHydrated] = useState(false);

  useEffect(() => {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) {
      try {
        setItems(JSON.parse(raw));
      } catch {
        // localStorage corrupto o de una versión anterior: se ignora y arranca vacío.
      }
    }
    setHydrated(true);
  }, []);

  useEffect(() => {
    if (hydrated) {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    }
  }, [items, hydrated]);

  function addItem(item: Omit<CartItem, "cantidad">, cantidad = 1) {
    setItems((prev) => {
      const existente = prev.find((i) => i.producto_id === item.producto_id);
      if (existente) {
        return prev.map((i) =>
          i.producto_id === item.producto_id ? { ...i, cantidad: i.cantidad + cantidad } : i
        );
      }
      return [...prev, { ...item, cantidad }];
    });
  }

  function removeItem(producto_id: number) {
    setItems((prev) => prev.filter((i) => i.producto_id !== producto_id));
  }

  function setCantidad(producto_id: number, cantidad: number) {
    if (cantidad < 1) {
      removeItem(producto_id);
      return;
    }
    setItems((prev) => prev.map((i) => (i.producto_id === producto_id ? { ...i, cantidad } : i)));
  }

  function clear() {
    setItems([]);
  }

  const totalItems = items.reduce((sum, i) => sum + i.cantidad, 0);
  const totalPrecio = items.reduce((sum, i) => sum + Number(i.precio_vta) * i.cantidad, 0);

  return (
    <CartContext.Provider value={{ items, totalItems, totalPrecio, addItem, removeItem, setCantidad, clear }}>
      {children}
    </CartContext.Provider>
  );
}

export function useCart(): CartContextValue {
  const ctx = useContext(CartContext);
  if (!ctx) throw new Error("useCart debe usarse dentro de <CartProvider>");
  return ctx;
}
