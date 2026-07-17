"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";

export default function LoginCallbackPage() {
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    // El backend manda los tokens en el fragmento (#...), que el navegador nunca
    // envía al servidor — evita que el JWT quede en logs de acceso de Next.js.
    const params = new URLSearchParams(window.location.hash.replace(/^#/, ""));
    const access_token = params.get("access_token");
    const refresh_token = params.get("refresh_token");
    const expires_in = Number(params.get("expires_in") ?? 3600);

    if (!access_token || !refresh_token) {
      setError("No se recibieron credenciales válidas de Google.");
      return;
    }

    fetch("/api/session", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ access_token, refresh_token, expires_in }),
    })
      .then((res) => {
        if (!res.ok) throw new Error("No se pudo iniciar sesión.");
        router.push("/");
        router.refresh();
      })
      .catch(() => setError("No se pudo completar el inicio de sesión con Google."));
  }, [router]);

  return (
    <main className="mx-auto flex min-h-screen max-w-sm flex-col items-center justify-center gap-4 px-4 text-center">
      {error ? (
        <>
          <p className="text-sm text-red-600">{error}</p>
          <a href="/login" className="text-sm underline">
            Volver al login
          </a>
        </>
      ) : (
        <p className="text-sm text-zinc-500">Completando inicio de sesión con Google…</p>
      )}
    </main>
  );
}
