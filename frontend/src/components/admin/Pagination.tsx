"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import type { Pager } from "@/types/admin";

/**
 * Controles Anterior/Siguiente para listados admin paginados server-side.
 * El número de página vive en el query string (?page_<grupo>=N) — CI4 lo lee
 * directo de ahí, así que solo hace falta armar el link, sin estado de React.
 */
export default function Pagination({ pager, pageParam }: { pager: Pager; pageParam: string }) {
  const searchParams = useSearchParams();

  if (pager.page_count <= 1) return null;

  function hrefForPage(page: number): string {
    const params = new URLSearchParams(searchParams.toString());
    params.set(pageParam, String(page));
    return `?${params.toString()}`;
  }

  const isFirst = pager.page <= 1;
  const isLast = pager.page >= pager.page_count;

  return (
    <div className="mt-4 flex items-center justify-between text-sm">
      <p className="text-zinc-500">
        Página {pager.page} de {pager.page_count} ({pager.total} resultados)
      </p>
      <div className="flex gap-2">
        <Link
          href={hrefForPage(pager.page - 1)}
          className={`rounded border px-3 py-1 ${isFirst ? "pointer-events-none opacity-40" : "hover:bg-zinc-50"}`}
        >
          Anterior
        </Link>
        <Link
          href={hrefForPage(pager.page + 1)}
          className={`rounded border px-3 py-1 ${isLast ? "pointer-events-none opacity-40" : "hover:bg-zinc-50"}`}
        >
          Siguiente
        </Link>
      </div>
    </div>
  );
}
