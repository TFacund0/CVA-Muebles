import { NextRequest, NextResponse } from "next/server";
import { getAccessToken } from "@/lib/auth";
import { API_URL } from "@/lib/api";

export async function POST(request: NextRequest) {
  const token = await getAccessToken();
  if (!token) {
    return NextResponse.json({ status: "error", message: "No autenticado." }, { status: 401 });
  }

  const payload = await request.json();

  const res = await fetch(`${API_URL}/ventas`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(payload),
  });

  const body = await res.json();
  return NextResponse.json(body, { status: res.status });
}
