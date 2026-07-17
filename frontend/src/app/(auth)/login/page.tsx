"use client";

import Link from "next/link";
import { Suspense, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { clientFetchJson } from "@/lib/clientFetch";
import { API_URL } from "@/lib/api";

function GoogleError() {
  const searchParams = useSearchParams();
  const googleError = searchParams.get("google_error");
  if (!googleError) return null;
  return <p className="text-sm text-red-600">{googleError}</p>;
}

export default function LoginPage() {
  const router = useRouter();
  const [loginValue, setLoginValue] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    setLoading(true);

    const result = await clientFetchJson("/api/login", "POST", { loginValue, password });
    setLoading(false);

    if (!result.ok) {
      setError(result.message ?? "No se pudo iniciar sesión.");
      return;
    }

    router.push("/");
    router.refresh();
  }

  return (
    <main className="mx-auto flex min-h-screen max-w-sm flex-col justify-center gap-4 px-4">
      <h1 className="text-2xl font-semibold">Iniciar sesión</h1>

      <Suspense fallback={null}>
        <GoogleError />
      </Suspense>

      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <input
          type="text"
          placeholder="Email o usuario"
          value={loginValue}
          onChange={(e) => setLoginValue(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        <input
          type="password"
          placeholder="Contraseña"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          className="rounded border px-3 py-2"
          required
        />
        {error && <p className="text-sm text-red-600">{error}</p>}
        <button
          type="submit"
          disabled={loading}
          className="rounded bg-black px-3 py-2 text-white disabled:opacity-50"
        >
          {loading ? "Ingresando..." : "Ingresar"}
        </button>
      </form>

      <div className="flex items-center gap-2 text-xs text-zinc-400">
        <div className="h-px flex-1 bg-zinc-200" />
        O
        <div className="h-px flex-1 bg-zinc-200" />
      </div>

      <a
        href={`${API_URL}/auth/google`}
        className="rounded border px-3 py-2 text-center text-sm hover:bg-zinc-50"
      >
        Continuar con Google
      </a>

      <p className="text-center text-sm text-zinc-500">
        ¿No tenés cuenta?{" "}
        <Link href="/registro" className="underline">
          Registrate
        </Link>
      </p>
    </main>
  );
}
