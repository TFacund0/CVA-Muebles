"use client";

import { useRouter, useSearchParams, usePathname } from "next/navigation";
import { useState } from "react";

export default function SearchBar({ placeholder = "Buscar productos..." }: { placeholder?: string }) {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const [value, setValue] = useState(searchParams.get("q") ?? "");

  function updateQuery(next: string) {
    setValue(next);
    const params = new URLSearchParams(searchParams.toString());
    if (next) {
      params.set("q", next);
    } else {
      params.delete("q");
    }
    router.push(`${pathname}?${params.toString()}`);
  }

  return (
    <div className="search-artisan w-full">
      <span className="mr-2 text-cva-text-muted">🔍</span>
      <input
        type="text"
        value={value}
        onChange={(e) => updateQuery(e.target.value)}
        placeholder={placeholder}
        className="w-full border-0 bg-transparent text-sm text-cva-brown outline-none placeholder:text-cva-text-muted"
      />
      {value && (
        <button
          type="button"
          onClick={() => updateQuery("")}
          className="ml-2 text-cva-text-muted transition hover:text-cva-brown"
          aria-label="Limpiar búsqueda"
        >
          ✕
        </button>
      )}
    </div>
  );
}
