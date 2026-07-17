"use client";

import { useState, type FormEvent } from "react";
import { useApiAction } from "@/lib/useApiAction";
import { clientFetch } from "@/lib/clientFetch";
import type { Perfil } from "@/lib/api";

export default function PerfilForm({ perfil }: { perfil: Perfil }) {
  const { error, loading, run } = useApiAction();
  const [usuario, setUsuario] = useState(perfil.usuario);
  const [nombre, setNombre] = useState(perfil.nombre);
  const [apellido, setApellido] = useState(perfil.apellido);
  const [email, setEmail] = useState(perfil.email);
  const [imagen, setImagen] = useState<File | null>(null);
  const [success, setSuccess] = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setSuccess(false);

    const formData = new FormData();
    formData.append("usuario", usuario);
    formData.append("nombre", nombre);
    formData.append("apellido", apellido);
    formData.append("email", email);
    if (imagen) formData.append("imagen", imagen);

    const result = await run(() => clientFetch("/api/perfil", { method: "POST", body: formData }));
    if (result?.ok) setSuccess(true);
  }

  return (
    <form onSubmit={handleSubmit} className="flex flex-col gap-3">
      <input
        type="text"
        value={usuario}
        onChange={(e) => setUsuario(e.target.value)}
        placeholder="Usuario"
        className="rounded border px-3 py-2"
        required
      />
      <input
        type="text"
        value={nombre}
        onChange={(e) => setNombre(e.target.value)}
        placeholder="Nombre"
        className="rounded border px-3 py-2"
        required
      />
      <input
        type="text"
        value={apellido}
        onChange={(e) => setApellido(e.target.value)}
        placeholder="Apellido"
        className="rounded border px-3 py-2"
        required
      />
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Email"
        className="rounded border px-3 py-2"
        required
      />
      <div>
        <label className="mb-1 block text-sm text-zinc-600">Foto de perfil (opcional)</label>
        <input
          type="file"
          accept="image/jpeg,image/png,image/webp"
          onChange={(e) => setImagen(e.target.files?.[0] ?? null)}
        />
      </div>
      {error && <p className="text-sm text-red-600">{error}</p>}
      {success && <p className="text-sm text-green-600">Perfil actualizado.</p>}
      <button
        type="submit"
        disabled={loading}
        className="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
      >
        {loading ? "Guardando..." : "Guardar cambios"}
      </button>
    </form>
  );
}
