import { afterEach, describe, expect, it, vi } from "vitest";
import { clientFetch, clientFetchJson } from "@/lib/clientFetch";

function mockFetchOnce(status: number, body: unknown) {
  vi.stubGlobal(
    "fetch",
    vi.fn().mockResolvedValue({
      status,
      ok: status >= 200 && status < 300,
      json: async () => body,
    })
  );
}

afterEach(() => {
  vi.unstubAllGlobals();
});

describe("clientFetch", () => {
  it("devuelve ok:true y los datos cuando la API responde success", async () => {
    mockFetchOnce(200, { status: "success", data: { id: 1 }, message: null });

    const result = await clientFetch<{ id: number }>("/api/lo-que-sea");

    expect(result.ok).toBe(true);
    expect(result.unauthorized).toBe(false);
    expect(result.data).toEqual({ id: 1 });
  });

  it("marca unauthorized:true en 401 sin intentar parsear un mensaje de error", async () => {
    mockFetchOnce(401, { status: "error", message: "No autenticado." });

    const result = await clientFetch("/api/protegido");

    expect(result.unauthorized).toBe(true);
    expect(result.ok).toBe(false);
  });

  it("marca unauthorized:true en 403 (mismo tratamiento que 401)", async () => {
    mockFetchOnce(403, { status: "error", message: "Prohibido." });

    const result = await clientFetch("/api/admin/algo");

    expect(result.unauthorized).toBe(true);
  });

  it("propaga el mensaje de error cuando la API responde status:error", async () => {
    mockFetchOnce(422, { status: "error", message: "Datos inválidos." });

    const result = await clientFetch("/api/algo");

    expect(result.ok).toBe(false);
    expect(result.unauthorized).toBe(false);
    expect(result.message).toBe("Datos inválidos.");
  });
});

describe("clientFetchJson", () => {
  it("manda Content-Type json y serializa el payload", async () => {
    mockFetchOnce(200, { status: "success", data: null, message: null });

    await clientFetchJson("/api/algo", "POST", { foo: "bar" });

    expect(fetch).toHaveBeenCalledWith(
      "/api/algo",
      expect.objectContaining({
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ foo: "bar" }),
      })
    );
  });
});
