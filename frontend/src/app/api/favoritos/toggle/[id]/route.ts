import { NextRequest, NextResponse } from "next/server";
import { getAccessToken } from "@/lib/auth";
import { API_URL } from "@/lib/api";

export async function POST(_request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const token = await getAccessToken();
  if (!token) {
    return NextResponse.json({ status: "error", message: "No autenticado." }, { status: 401 });
  }

  const { id } = await params;

  const res = await fetch(`${API_URL}/favoritos/toggle/${id}`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
  });

  const body = await res.json();
  return NextResponse.json(body, { status: res.status });
}
