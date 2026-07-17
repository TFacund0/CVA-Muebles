"use client";

import { useEffect, useState, type FormEvent } from "react";
import { useRouter } from "next/navigation";

export default function RegistroGooglePage() {
  const router = useRouter();
  const [token, setToken] = useState<string | null>(null);
  const [nombre, setNombre] = useState("");
  const [email, setEmail] = useState("");
  const [user, setUser] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Igual que /login/callback: estos datos vienen por fragmento (#...), nunca
    // pasan por el servidor. Solo son para mostrar contexto — el email real que
    // se usa para crear la cuenta lo valida el backend contra el `token` cacheado.
    const params = new URLSearchParams(window.location.hash.replace(/^#/, ""));
    const t = params.get("token");

    if (!t) {
      setError("La sesión de Google expiró. Volvé a intentar desde el login.");
      return;
    }

    setToken(t);
    setNombre(`${params.get("nombre") ?? ""} ${params.get("apellido") ?? ""}`.trim());
    setEmail(params.get("email") ?? "");
  }, []);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);

    if (!token) return;

    setLoading(true);
    const res = await fetch("/api/register/google", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ token, user }),
    });
    const body = await res.json();
    setLoading(false);

    if (!res.ok || body.status === "error") {
      setError(body.message ?? "No se pudo completar el registro.");
      return;
    }

    router.push("/");
    router.refresh();
  }

  return (
    <main className="mx-auto flex min-h-screen max-w-sm flex-col justify-center gap-4 px-4">
      <h1 className="text-2xl font-semibold">Completar registro</h1>

      {email && (
        <p className="text-sm text-zinc-500">
          Estás creando una cuenta para <strong>{nombre || email}</strong> ({email}) con Google. Elegí un
          nombre de usuario para terminar.
        </p>
      )}

      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <input
          type="text"
          placeholder="Nombre de usuario (mínimo 4 caracteres)"
          value={user}
          onChange={(e) => setUser(e.target.value)}
          className="rounded border px-3 py-2"
          minLength={4}
          required
        />
        {error && <p className="text-sm text-red-600">{error}</p>}
        <button
          type="submit"
          disabled={loading || !token}
          className="rounded bg-black px-3 py-2 text-white disabled:opacity-50"
        >
          {loading ? "Creando cuenta..." : "Finalizar registro"}
        </button>
      </form>
    </main>
  );
}
