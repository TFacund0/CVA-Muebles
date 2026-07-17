import Link from "next/link";

export const metadata = { title: "Comercialización — CVA Muebles" };

const PAGOS = [
  { titulo: "Efectivo", descripcion: "Bonificaciones especiales en taller o contra-entrega." },
  { titulo: "Tarjetas de Crédito", descripcion: "Hasta 12 cuotas fijas con todos los bancos." },
  { titulo: "Transferencia", descripcion: "Bancaria o vía Mercado Pago de forma inmediata." },
  { titulo: "Financiación", descripcion: "Planes a medida para proyectos especiales." },
];

const LOGISTICA = [
  { paso: "01", titulo: "Fabricación Cuidadosa", descripcion: "Respetamos los 15-30 días de creación artesanal." },
  { paso: "02", titulo: "Embalaje de Alta Resistencia", descripcion: "Protección extrema para cada veta y arista." },
  { paso: "03", titulo: "Entrega Especializada", descripcion: "Personal capacitado para el manejo de muebles pesados." },
];

export default function ComercializacionPage() {
  return (
    <main className="mx-auto max-w-3xl px-6 py-12">
      <header className="mb-10 text-center">
        <p className="text-sm font-bold uppercase tracking-widest text-amber-700">Transparencia y Confianza</p>
        <h1 className="mt-2 text-4xl font-bold">Comercialización</h1>
        <p className="mt-4 text-lg text-zinc-600">
          Descubrí el camino que recorre cada una de nuestras piezas desde el taller hasta tu hogar.
        </p>
      </header>

      <section className="mb-10 text-center">
        <h2 className="text-2xl font-bold">El Compromiso CVA</h2>
        <p className="mx-auto mt-3 max-w-xl text-zinc-600">
          En <strong>CVA Muebles</strong> nos esforzamos por hacer que la adquisición de nuestros productos sea
          tan placentera como su uso. Cada pieza es tratada con el respeto que merece la madera noble.
        </p>
      </section>

      <section className="mb-10">
        <p className="text-sm font-bold uppercase tracking-wide text-amber-700">Inversión Segura</p>
        <h2 className="mt-1 mb-4 text-2xl font-bold">Formas de Pago</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          {PAGOS.map((p) => (
            <div key={p.titulo} className="rounded-lg border p-4">
              <h3 className="font-semibold">{p.titulo}</h3>
              <p className="text-sm text-zinc-500">{p.descripcion}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="mb-10">
        <p className="text-sm font-bold uppercase tracking-wide text-amber-700">Del Taller a tu Casa</p>
        <h2 className="mt-1 mb-4 text-2xl font-bold">Logística Artesanal</h2>
        <p className="mb-4 text-zinc-600">
          Entendemos que un mueble de autor requiere un traslado a su altura. No somos solo un flete, somos
          custodios de tu pieza.
        </p>
        <div className="flex flex-col gap-3">
          {LOGISTICA.map((l) => (
            <div key={l.paso} className="flex items-center gap-4">
              <span className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-zinc-900 text-sm font-bold text-white">
                {l.paso}
              </span>
              <div>
                <h3 className="font-semibold">{l.titulo}</h3>
                <p className="text-sm text-zinc-500">{l.descripcion}</p>
              </div>
            </div>
          ))}
        </div>
        <div className="mt-6 rounded-lg bg-zinc-50 p-4">
          <h3 className="font-semibold">Área de Cobertura</h3>
          <p className="mt-1 text-sm text-zinc-600">
            Llegamos a toda la provincia de Corrientes con logística propia. Consultanos por envíos
            nacionales. Retiro en taller: Mantilla, Corrientes (sin costo adicional).
          </p>
        </div>
      </section>

      <section className="rounded-lg border p-6 text-center">
        <p className="text-sm font-bold uppercase tracking-wide text-amber-700">Seguridad y Garantía</p>
        <h2 className="mt-1 mb-4 text-2xl font-bold">Compromiso para Generaciones</h2>
        <p className="mx-auto max-w-xl text-zinc-600">
          Cada mueble CVA cuenta con <strong>1 año de garantía estructural</strong>. Nuestra meta es que tu
          única preocupación sea disfrutar de la calidez de la madera en tu hogar.
        </p>
        <Link href="/terminos" className="mt-4 inline-block rounded-full border px-6 py-3 font-semibold">
          Ver términos y condiciones
        </Link>
      </section>
    </main>
  );
}
