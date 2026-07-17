"use client";

import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { adminMutate } from "@/lib/adminClient";
import { useAdminAction } from "@/lib/useAdminAction";
import type { Categoria } from "@/lib/api";
import type { AdminProducto } from "@/lib/admin";

export default function ProductoForm({
  categorias,
  producto,
}: {
  categorias: Categoria[];
  producto?: AdminProducto;
}) {
  const router = useRouter();
  const { error, loading, run } = useAdminAction();
  const isEdit = Boolean(producto);

  const [nombreProd, setNombreProd] = useState(producto?.nombre_prod ?? "");
  const [categoriaId, setCategoriaId] = useState(String(producto?.categoria_id ?? categorias[0]?.id_categoria ?? ""));
  const [precio, setPrecio] = useState(producto?.precio ?? "");
  const [precioVta, setPrecioVta] = useState(producto?.precio_vta ?? "");
  const [stock, setStock] = useState(String(producto?.stock ?? ""));
  const [stockMin, setStockMin] = useState(String(producto?.stock_min ?? "0"));
  const [descripcion, setDescripcion] = useState(producto?.descripcion ?? "");
  const [imagen, setImagen] = useState<File | null>(null);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("nombre_prod", nombreProd);
    formData.append("categoria_id", categoriaId);
    formData.append("precio", precio);
    formData.append("precio_vta", precioVta);
    formData.append("stock", stock);
    formData.append("stock_min", stockMin);
    formData.append("descripcion", descripcion);
    if (imagen) formData.append("imagen", imagen);

    const path = isEdit ? `/productos/${producto!.id_producto}` : "/productos";
    const result = await run(() => adminMutate(path, { method: "POST", body: formData }));

    if (result?.ok) router.push("/admin/productos");
  }

  return (
    <form onSubmit={handleSubmit} className="flex max-w-lg flex-col gap-3">
      <input
        type="text"
        placeholder="Nombre del producto"
        value={nombreProd}
        onChange={(e) => setNombreProd(e.target.value)}
        className="rounded border px-3 py-2"
        required
      />
      <select
        value={categoriaId}
        onChange={(e) => setCategoriaId(e.target.value)}
        className="rounded border px-3 py-2"
        required
      >
        {categorias.map((cat) => (
          <option key={cat.id_categoria} value={cat.id_categoria}>
            {cat.descripcion}
          </option>
        ))}
      </select>
      <div className="grid grid-cols-2 gap-3">
        <input
          type="number"
          step="0.01"
          placeholder="Precio de costo"
          value={precio}
          onChange={(e) => setPrecio(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="number"
          step="0.01"
          placeholder="Precio de venta"
          value={precioVta}
          onChange={(e) => setPrecioVta(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="number"
          placeholder="Stock"
          value={stock}
          onChange={(e) => setStock(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="number"
          placeholder="Stock mínimo"
          value={stockMin}
          onChange={(e) => setStockMin(e.target.value)}
          className="rounded border px-3 py-2"
        />
      </div>
      <textarea
        placeholder="Descripción"
        value={descripcion}
        onChange={(e) => setDescripcion(e.target.value)}
        rows={4}
        className="rounded border px-3 py-2"
      />
      <div>
        <label className="mb-1 block text-sm text-zinc-600">
          Imagen principal {isEdit && "(dejar vacío para mantener la actual)"}
        </label>
        <input
          type="file"
          accept="image/jpeg,image/png,image/webp"
          onChange={(e) => setImagen(e.target.files?.[0] ?? null)}
        />
      </div>

      {error && <p className="text-sm text-red-600">{error}</p>}

      <button
        type="submit"
        disabled={loading}
        className="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
      >
        {loading ? "Guardando..." : isEdit ? "Guardar cambios" : "Crear producto"}
      </button>
    </form>
  );
}
