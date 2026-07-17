import { describe, expect, it } from "vitest";
import { formatEstado, formatFecha, formatMoneda } from "@/lib/format";

describe("formatEstado", () => {
  it("traduce los estados conocidos", () => {
    expect(formatEstado("PENDIENTE")).toBe("Pendiente");
    expect(formatEstado("EN_PROCESO")).toBe("En Proceso");
    expect(formatEstado("TERMINADO")).toBe("Terminado");
    expect(formatEstado("ENTREGADO")).toBe("Entregado");
  });

  it("devuelve el string original si el estado no está mapeado", () => {
    expect(formatEstado("ACEPTADO")).toBe("ACEPTADO");
    expect(formatEstado("")).toBe("");
  });
});

describe("formatFecha", () => {
  it("formatea una fecha ISO al formato es-AR", () => {
    // 2026-03-05 en cualquier huso horario cercano a AR debería seguir siendo marzo.
    const resultado = formatFecha("2026-03-05T12:00:00");
    expect(resultado).toMatch(/5.*3.*2026|05\/03\/2026/);
  });
});

describe("formatMoneda", () => {
  it("formatea números con 2 decimales y el prefijo $", () => {
    expect(formatMoneda(1500)).toBe("$1500.00");
    expect(formatMoneda("1500.5")).toBe("$1500.50");
    expect(formatMoneda(0)).toBe("$0.00");
  });

  it("redondea a 2 decimales", () => {
    expect(formatMoneda(19.999)).toBe("$20.00");
  });
});
