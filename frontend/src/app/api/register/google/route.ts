import { NextRequest, NextResponse } from "next/server";
import { API_URL } from "@/lib/api";
import { setAuthCookies } from "@/lib/authCookies";

/**
 * Completa el registro de una cuenta originada en Google (POST /api/v1/auth/google/completar
 * en el backend). El backend valida el `token` de un solo uso contra su caché — este
 * endpoint solo reenvía la request y, si sale bien, setea las cookies httpOnly.
 */
export async function POST(request: NextRequest) {
  const body = await request.json();

  const res = await fetch(`${API_URL}/auth/google/completar`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
  });

  const result = await res.json();

  if (!res.ok || result.status === "error") {
    return NextResponse.json(
      { status: "error", message: result.message ?? "No se pudo completar el registro." },
      { status: res.status }
    );
  }

  const response = NextResponse.json({ status: "success" }, { status: 201 });
  setAuthCookies(response, result.data);

  return response;
}
