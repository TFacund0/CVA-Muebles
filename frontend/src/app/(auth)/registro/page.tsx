"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { clientFetchJson } from "@/lib/clientFetch";

export default function RegistroPage() {
  const router = useRouter();
  const [user, setUser] = useState("");
  const [name, setName] = useState("");
  const [surname, setSurname] = useState("");
  const [email, setEmail] = useState("");
  const [pass, setPass] = useState("");
  const [terms, setTerms] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);

    if (pass.length < 8) {
      setError("La contraseña debe tener al menos 8 caracteres.");
      return;
    }
    if (!terms) {
      setError("Tenés que aceptar los Términos y Condiciones.");
      return;
    }

    setLoading(true);
    const result = await clientFetchJson("/api/register", "POST", { user, name, surname, email, pass });
    setLoading(false);

    if (!result.ok) {
      setError(result.message ?? "No se pudo completar el registro.");
      return;
    }

    router.push("/");
    router.refresh();
  }

  return (
    <main className="mx-auto flex min-h-screen max-w-sm flex-col justify-center gap-4 px-4 py-12">
      <h1 className="text-2xl font-semibold">Crear cuenta</h1>
      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <input
          type="text"
          placeholder="Nombre de usuario"
          value={user}
          onChange={(e) => setUser(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="text"
          placeholder="Nombre"
          value={name}
          onChange={(e) => setName(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="text"
          placeholder="Apellido"
          value={surname}
          onChange={(e) => setSurname(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="email"
          placeholder="correo@ejemplo.com"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="password"
          placeholder="Mínimo 8 caracteres"
          value={pass}
          onChange={(e) => setPass(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />

        <label className="flex items-start gap-2 text-sm text-zinc-600">
          <input
            type="checkbox"
            checked={terms}
            onChange={(e) => setTerms(e.target.checked)}
            className="mt-1"
            required
          />
          <span>
            Acepto los{" "}
            <Link href="/terminos" target="_blank" className="font-medium underline">
              Términos y Condiciones
            </Link>
          </span>
        </label>

        {error && <p className="text-sm text-red-600">{error}</p>}

        <button
          type="submit"
          disabled={loading}
          className="rounded bg-black px-3 py-2 text-white disabled:opacity-50"
        >
          {loading ? "Creando cuenta..." : "Crear cuenta"}
        </button>
      </form>
      <p className="text-center text-sm text-zinc-500">
        ¿Ya tenés cuenta?{" "}
        <Link href="/login" className="underline">
          Iniciá sesión
        </Link>
      </p>
    </main>
  );
}
