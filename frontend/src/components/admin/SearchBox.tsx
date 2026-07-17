"use client";

import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { useState, type FormEvent } from "react";

/**
 * Caja de búsqueda que empuja el término al query string (?search=...) en vez
 * de filtrar en memoria — necesario porque las listas admin ahora vienen
 * paginadas server-side (solo se tiene la página actual en el cliente, no
 * el listado completo para filtrar localmente).
 */
export default function SearchBox({
  placeholder,
  pageParam,
}: {
  placeholder: string;
  pageParam: string;
}) {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const [value, setValue] = useState(searchParams.get("search") ?? "");

  function handleSubmit(e: FormEvent) {
    e.preventDefault();
    const params = new URLSearchParams(searchParams.toString());
    if (value) {
      params.set("search", value);
    } else {
      params.delete("search");
    }
    params.delete(pageParam); // toda búsqueda nueva vuelve a la página 1
    router.push(`${pathname}?${params.toString()}`);
  }

  return (
    <form onSubmit={handleSubmit} className="mb-4 flex max-w-sm gap-2">
      <input
        type="text"
        placeholder={placeholder}
        value={value}
        onChange={(e) => setValue(e.target.value)}
        className="flex-1 rounded border px-3 py-2"
      />
      <button type="submit" className="rounded border px-3 py-2 text-sm hover:bg-zinc-50">
        Buscar
      </button>
    </form>
  );
}
