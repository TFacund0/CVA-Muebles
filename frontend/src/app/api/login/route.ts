import { NextRequest, NextResponse } from "next/server";
import { login } from "@/lib/api";
import { setAuthCookies } from "@/lib/authCookies";

export async function POST(request: NextRequest) {
  const { loginValue, password } = await request.json();

  try {
    const result = await login(loginValue, password);

    const response = NextResponse.json({ status: "success", user: result.user });
    setAuthCookies(response, result);

    return response;
  } catch (error) {
    const message = error instanceof Error ? error.message : "Error al iniciar sesión.";
    return NextResponse.json({ status: "error", message }, { status: 401 });
  }
}
