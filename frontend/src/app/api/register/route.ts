import { NextRequest, NextResponse } from "next/server";
import { register, type RegisterData } from "@/lib/api";
import { setAuthCookies } from "@/lib/authCookies";

export async function POST(request: NextRequest) {
  const data: RegisterData = await request.json();

  try {
    const result = await register(data);

    const response = NextResponse.json({ status: "success", user: result.user }, { status: 201 });
    setAuthCookies(response, result);

    return response;
  } catch (error) {
    const message = error instanceof Error ? error.message : "No se pudo completar el registro.";
    return NextResponse.json({ status: "error", message }, { status: 422 });
  }
}
