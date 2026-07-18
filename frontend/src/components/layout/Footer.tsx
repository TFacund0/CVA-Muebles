import Link from "next/link";

export default function Footer() {
  return (
    <footer
      className="mt-auto border-t-4 border-cva-gold bg-cva-brown bg-cover bg-center text-white"
      style={{
        backgroundImage: "linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.9)), url(/img/madera.jpg)",
      }}
    >
      <div className="mx-auto grid max-w-6xl gap-10 px-6 py-14 sm:grid-cols-3">
        <div>
          <h3 className="font-heading mb-4 text-lg font-bold text-cva-gold-light">Contacto</h3>
          <p className="text-sm text-white/80">📍 Corrientes, Argentina</p>
          <a
            href="https://wa.me/5493794098511"
            target="_blank"
            rel="noreferrer"
            className="mt-2 inline-block text-sm text-white/80 hover:text-cva-gold-light"
          >
            💬 WhatsApp
          </a>
        </div>

        <div>
          <h3 className="font-heading mb-4 text-lg font-bold text-cva-gold-light">Sobre Nosotros</h3>
          <p className="text-sm text-white/80">Carpintería de autor, muebles hechos a medida.</p>
          <Link
            href="/quienes-somos"
            className="mt-4 inline-block rounded-full border border-white/40 px-4 py-1.5 text-sm transition hover:border-cva-gold hover:text-cva-gold-light"
          >
            Conocenos
          </Link>
        </div>

        <div>
          <h3 className="font-heading mb-4 text-lg font-bold text-cva-gold-light">Enlaces</h3>
          <div className="flex flex-col gap-2 text-sm text-white/80">
            <Link href="/terminos" className="hover:text-cva-gold-light">
              Términos y Condiciones
            </Link>
            <Link href="/contacto" className="hover:text-cva-gold-light">
              Contacto
            </Link>
          </div>
        </div>
      </div>

      <div className="border-t border-white/10 px-6 py-4 text-center text-xs text-white/60">
        © {new Date().getFullYear()} CVA Muebles. Carpintería de Autor.
      </div>
    </footer>
  );
}
