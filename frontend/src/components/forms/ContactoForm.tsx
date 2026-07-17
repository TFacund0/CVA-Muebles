"use client";

import { useState, type FormEvent } from "react";
import { enviarConsulta } from "@/lib/api";

const ASUNTOS = [
  { value: "Presupuesto", label: "Presupuesto para Mueble" },
  { value: "Pedido", label: "Consulta sobre mi Pedido" },
  { value: "Garantia", label: "Garantía y Soporte" },
  { value: "Otro", label: "Otros motivos" },
];

export default function ContactoForm() {
  const [nombre, setNombre] = useState("");
  const [apellido, setApellido] = useState("");
  const [email, setEmail] = useState("");
  const [telefono, setTelefono] = useState("");
  const [asunto, setAsunto] = useState("");
  const [descripcion, setDescripcion] = useState("");
  const [middleName, setMiddleName] = useState(""); // honeypot, debe quedar vacío
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();

    if (middleName) return; // bot detectado, no se envía nada

    setError(null);
    setLoading(true);

    try {
      await enviarConsulta({ nombre, apellido, email, telefono, asunto, descripcion });
      setSuccess(true);
      setNombre("");
      setApellido("");
      setEmail("");
      setTelefono("");
      setAsunto("");
      setDescripcion("");
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudo enviar la consulta.");
    } finally {
      setLoading(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} className="flex flex-col gap-4">
      <div style={{ display: "none" }}>
        <label>Si eres humano, deja esto vacío</label>
        <input
          type="text"
          value={middleName}
          onChange={(e) => setMiddleName(e.target.value)}
          tabIndex={-1}
          autoComplete="off"
        />
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <input
          type="text"
          placeholder="Nombre"
          value={nombre}
          onChange={(e) => setNombre(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="text"
          placeholder="Apellido"
          value={apellido}
          onChange={(e) => setApellido(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="tel"
          placeholder="Teléfono"
          value={telefono}
          onChange={(e) => setTelefono(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
      </div>

      <select
        value={asunto}
        onChange={(e) => setAsunto(e.target.value)}
        className="rounded border px-3 py-2"
        required
      >
        <option value="" disabled>
          Seleccioná un motivo
        </option>
        {ASUNTOS.map((a) => (
          <option key={a.value} value={a.value}>
            {a.label}
          </option>
        ))}
      </select>

      <textarea
        placeholder="¿Cómo podemos ayudarte hoy?"
        value={descripcion}
        onChange={(e) => setDescripcion(e.target.value)}
        rows={6}
        className="rounded border px-3 py-2"
        required
      />

      {error && <p className="text-sm text-red-600">{error}</p>}
      {success && (
        <p className="text-sm text-green-600">
          ¡Gracias! Tu consulta fue enviada, te responderemos a la brevedad.
        </p>
      )}

      <button
        type="submit"
        disabled={loading}
        className="rounded bg-black px-6 py-3 text-white disabled:opacity-50"
      >
        {loading ? "Enviando..." : "Enviar Consulta"}
      </button>
    </form>
  );
}
