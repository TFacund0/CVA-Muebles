export const metadata = { title: "Términos y Condiciones — CVA Muebles" };

export default function TerminosPage() {
  return (
    <main className="mx-auto max-w-3xl px-6 py-12">
      <header className="mb-10 text-center">
        <h1 className="text-4xl font-bold">Marco Legal</h1>
        <p className="mt-4 text-lg text-zinc-600">
          Nuestros compromisos y tus derechos en un lenguaje claro y artesanal.
        </p>
      </header>

      <section className="mb-8">
        <h2 className="text-2xl font-bold">Política de Privacidad</h2>
        <p className="mt-3 text-zinc-600">
          En CVA Muebles tratamos tu información con el mismo respeto que le damos a la madera nativa.
          Recopilamos datos mínimos necesarios para la entrega de tus productos y la mejora de nuestra
          atención. No compartimos tus datos con terceros sin tu consentimiento explícito.
        </p>
        <p className="mt-3 text-sm font-semibold text-zinc-700">
          Dato Clave: Tu información está protegida bajo estándares de encriptación modernos.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="text-2xl font-bold">Términos de Venta</h2>
        <div className="mt-3">
          <h3 className="font-semibold">Fabricación</h3>
          <p className="text-sm text-zinc-500">
            Iniciamos la producción artesanal una vez acreditado el 50% del presupuesto.
          </p>
        </div>
        <div className="mt-3">
          <h3 className="font-semibold">Envíos</h3>
          <p className="text-sm text-zinc-500">
            La logística es coordinada según disponibilidad del taller y zona de entrega.
          </p>
        </div>
      </section>

      <section className="mb-8">
        <h2 className="text-2xl font-bold">Garantías y Soporte</h2>
        <p className="mt-3 text-zinc-600">
          Ofrecemos un compromiso de 1 año de garantía estructural sobre defectos de fabricación en
          condiciones normales de uso.
        </p>
      </section>

      <section>
        <h2 className="text-2xl font-bold">Uso del Sitio Web</h2>
        <p className="mt-3 text-zinc-600">
          Todos los diseños y fotografías son propiedad intelectual de CVA Muebles y no pueden reproducirse
          sin autorización.
        </p>
        <p className="mt-6 text-xs text-zinc-400">Documento validado para 2026.</p>
      </section>
    </main>
  );
}
