import { NextRequest, NextResponse } from "next/server";
import { getAccessToken } from "@/lib/auth";
import { API_URL } from "@/lib/api";

/**
 * Proxy genérico para todos los endpoints /api/v1/admin/* del backend.
 * Evita repetir un route handler por cada acción admin (crear/editar/eliminar/etc.):
 * lee la cookie httpOnly server-side, reenvía la request tal cual (JSON o multipart)
 * con el Bearer token, y devuelve la respuesta de la API PHP sin transformarla.
 */
async function proxy(request: NextRequest, path: string[]) {
  const token = await getAccessToken();
  if (!token) {
    return NextResponse.json({ status: "error", message: "No autenticado." }, { status: 401 });
  }

  const targetUrl = `${API_URL}/admin/${path.join("/")}${request.nextUrl.search}`;
  const contentType = request.headers.get("content-type") ?? "";

  const init: RequestInit = {
    method: request.method,
    headers: { Authorization: `Bearer ${token}` },
  };

  if (request.method !== "GET" && request.method !== "DELETE") {
    if (contentType.includes("multipart/form-data")) {
      init.body = await request.formData();
    } else {
      init.headers = { ...init.headers, "Content-Type": "application/json" };
      init.body = await request.text();
    }
  }

  const res = await fetch(targetUrl, init);
  const body = await res.json();
  return NextResponse.json(body, { status: res.status });
}

export async function GET(request: NextRequest, { params }: { params: Promise<{ path: string[] }> }) {
  return proxy(request, (await params).path);
}

export async function POST(request: NextRequest, { params }: { params: Promise<{ path: string[] }> }) {
  return proxy(request, (await params).path);
}

export async function PUT(request: NextRequest, { params }: { params: Promise<{ path: string[] }> }) {
  return proxy(request, (await params).path);
}

export async function DELETE(request: NextRequest, { params }: { params: Promise<{ path: string[] }> }) {
  return proxy(request, (await params).path);
}
