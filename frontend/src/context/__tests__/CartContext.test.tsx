import { act, renderHook, waitFor } from "@testing-library/react";
import { beforeEach, describe, expect, it } from "vitest";
import { CartProvider, useCart } from "@/context/CartContext";
import type { ReactNode } from "react";

const wrapper = ({ children }: { children: ReactNode }) => <CartProvider>{children}</CartProvider>;

const producto1 = {
  producto_id: 1,
  nombre_prod: "Mesa Ratona",
  imagen: "mesa.jpg",
  precio_vta: "1000.00",
};

const producto2 = {
  producto_id: 2,
  nombre_prod: "Silla",
  imagen: "silla.jpg",
  precio_vta: "500.50",
};

beforeEach(() => {
  localStorage.clear();
});

describe("CartContext", () => {
  it("arranca vacío", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));
    expect(result.current.totalItems).toBe(0);
    expect(result.current.totalPrecio).toBe(0);
  });

  it("agrega un producto nuevo con cantidad 1 por defecto", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1));

    expect(result.current.items).toHaveLength(1);
    expect(result.current.items[0].cantidad).toBe(1);
    expect(result.current.totalItems).toBe(1);
  });

  it("si se agrega el mismo producto de nuevo, suma la cantidad en vez de duplicar la fila", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1));
    act(() => result.current.addItem(producto1, 2));

    expect(result.current.items).toHaveLength(1);
    expect(result.current.items[0].cantidad).toBe(3);
  });

  it("calcula el total en pesos multiplicando precio por cantidad de cada línea", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1, 2)); // 1000 * 2 = 2000
    act(() => result.current.addItem(producto2, 1)); // 500.5 * 1 = 500.5

    expect(result.current.totalPrecio).toBeCloseTo(2500.5);
    expect(result.current.totalItems).toBe(3);
  });

  it("setCantidad a 0 o negativo elimina el ítem en vez de dejar una cantidad inválida", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1));
    act(() => result.current.setCantidad(producto1.producto_id, 0));

    expect(result.current.items).toHaveLength(0);
  });

  it("removeItem saca solo el producto indicado", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1));
    act(() => result.current.addItem(producto2));
    act(() => result.current.removeItem(producto1.producto_id));

    expect(result.current.items).toHaveLength(1);
    expect(result.current.items[0].producto_id).toBe(producto2.producto_id);
  });

  it("clear() vacía el carrito por completo", async () => {
    const { result } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1));
    act(() => result.current.addItem(producto2));
    act(() => result.current.clear());

    expect(result.current.items).toEqual([]);
  });

  it("persiste el carrito en localStorage y lo restaura en un nuevo provider", async () => {
    const { result, unmount } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result.current.items).toEqual([]));

    act(() => result.current.addItem(producto1, 4));
    await waitFor(() => expect(localStorage.getItem("cva_cart")).toContain("Mesa Ratona"));
    unmount();

    const { result: result2 } = renderHook(() => useCart(), { wrapper });
    await waitFor(() => expect(result2.current.items).toHaveLength(1));
    expect(result2.current.items[0].cantidad).toBe(4);
  });
});
