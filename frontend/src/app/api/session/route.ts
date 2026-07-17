import { NextRequest, NextResponse } from "next/server";
import { setAuthCookies } from "@/lib/authCookies";

interface TokenPayload {
  access_token: string;
  refresh_token: string;
  expires_in: number;
}

/**
 * Establece la sesión (cookies httpOnly) a partir de un JWT ya emitido por el
 * backend — usado por el callback de Google login, que recibe los tokens en el
 * fragmento de la URL (nunca llega al servidor) y se los pasa a este endpoint
 * desde el cliente para poder setear las cookies httpOnly reales.
 */
export async function POST(request: NextRequest) {
  const tokens: Partial<TokenPayload> = await request.json();

  if (!tokens.access_token || !tokens.refresh_token) {
    return NextResponse.json({ status: "error", message: "Tokens inválidos." }, { status: 422 });
  }

  const response = NextResponse.json({ status: "success" });
  setAuthCookies(response, {
    access_token: tokens.access_token,
    refresh_token: tokens.refresh_token,
    expires_in: tokens.expires_in ?? 3600,
  });

  return response;
}
