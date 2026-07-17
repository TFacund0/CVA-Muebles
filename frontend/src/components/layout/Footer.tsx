import Link from "next/link";

export default function Footer() {
  return (
    <footer className="mt-auto border-t py-8">
      <div className="mx-auto flex max-w-5xl flex-col gap-3 px-6 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between">
        <p>© {new Date().getFullYear()} CVA Muebles. Carpintería de Autor.</p>
        <div className="flex gap-4">
          <Link href="/terminos" className="hover:underline">
            Términos y Condiciones
          </Link>
          <Link href="/contacto" className="hover:underline">
            Contacto
          </Link>
        </div>
      </div>
    </footer>
  );
}
