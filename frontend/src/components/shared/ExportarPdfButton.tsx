"use client";

import { useState } from "react";

export default function ExportarPdfButton({
  targetId,
  filename,
}: {
  targetId: string;
  filename: string;
}) {
  const [loading, setLoading] = useState(false);

  async function handleExport() {
    setLoading(true);
    const element = document.getElementById(targetId);
    if (!element) {
      setLoading(false);
      return;
    }

    const html2pdf = (await import("html2pdf.js")).default;
    await html2pdf()
      .set({ margin: 10, filename, image: { type: "jpeg", quality: 0.98 } })
      .from(element)
      .save();

    setLoading(false);
  }

  return (
    <button
      onClick={handleExport}
      disabled={loading}
      className="mt-4 rounded border px-4 py-2 text-sm transition hover:bg-zinc-50 disabled:opacity-50"
    >
      {loading ? "Generando PDF..." : "Exportar PDF"}
    </button>
  );
}
