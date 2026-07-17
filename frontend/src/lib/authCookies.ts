import { NextResponse } from "next/server";
import type { LoginResponse } from "@/lib/api";

type TokenPair = Pick<LoginResponse, "access_token" | "refresh_token" | "expires_in">;

/**
 * Setea las cookies httpOnly de sesión (access + refresh token) en la respuesta de
 * un Route Handler. Compartido entre /api/login, /api/register, /api/session
 * (Google login) y /api/register/google — todos terminan con el mismo par de tokens.
 */
export function setAuthCookies(response: NextResponse, result: TokenPair): void {
  response.cookies.set("cva_access_token", result.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    maxAge: result.expires_in,
    path: "/",
  });
  response.cookies.set("cva_refresh_token", result.refresh_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
  });
}
