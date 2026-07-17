import Link from "next/link";

export const metadata = { title: "Beneficios — CVA Muebles" };

const BENEFICIOS = [
  {
    titulo: "Maderas de Primera",
    descripcion: "Seleccionamos cada tabla para asegurar durabilidad y belleza natural.",
  },
  {
    titulo: "Pasión Artesanal",
    descripcion: "No usamos procesos industriales masivos. Cada mueble tiene alma propia.",
  },
  {
    titulo: "Trato Cercano",
    descripcion: "Atención personalizada directa con el artesano para tus proyectos especiales.",
  },
];

export default function BeneficiosPage() {
  return (
    <main className="mx-auto max-w-3xl px-6 py-12 text-center">
      <p className="text-sm font-bold uppercase tracking-widest text-amber-700">Experiencia CVA Muebles</p>
      <h1 className="mt-2 text-4xl font-bold">Nuestros Compromisos</h1>
      <p className="mx-auto mt-4 max-w-xl text-lg text-zinc-600">
        En nuestra carpintería, cada pieza es una promesa de calidad y dedicación artesanal.
      </p>

      <div className="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
        {BENEFICIOS.map((b) => (
          <div key={b.titulo} className="rounded-lg border p-6">
            <h2 className="font-semibold">{b.titulo}</h2>
            <p className="mt-2 text-sm text-zinc-500">{b.descripcion}</p>
          </div>
        ))}
      </div>

      <div className="mt-12">
        <h3 className="text-xl font-semibold">¿Buscás algo a medida?</h3>
        <p className="mt-1 text-zinc-600">Estamos listos para hacer realidad tu idea en madera.</p>
        <Link href="/contacto" className="mt-4 inline-block rounded-full bg-black px-6 py-3 text-white">
          Contactar ahora
        </Link>
      </div>
    </main>
  );
}
