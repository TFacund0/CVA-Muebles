export const metadata = { title: "Quiénes Somos — CVA Muebles" };

export default function QuienesSomosPage() {
  return (
    <main className="mx-auto max-w-3xl px-6 py-12">
      <header className="mb-10 text-center">
        <p className="text-sm font-bold uppercase tracking-widest text-amber-700">Tradición Familiar</p>
        <h1 className="mt-2 text-4xl font-bold">El Alma de CVA</h1>
        <p className="mt-4 text-lg text-zinc-600">
          Más que muebles, creamos legados tallados en la nobleza de la madera correntina.
        </p>
      </header>

      <section className="mb-10">
        <p className="text-sm font-bold uppercase tracking-wide text-amber-700">Nuestros Inicios</p>
        <h2 className="mt-1 text-2xl font-bold">Legado y Tradición</h2>
        <p className="mt-4 text-zinc-600">
          CVA Muebles nace en el corazón de Mantilla, Corrientes, como un tributo al oficio artesano y la
          nobleza de la madera. Bajo la visión de <strong>César Víctor Acevedo</strong>, nuestro taller se ha
          convertido en un referente de la <em>Carpintería de Autor</em>.
        </p>
        <p className="mt-3 text-zinc-600">
          Lo que comenzó como una pasión familiar por transformar la materia prima, hoy es una realidad que
          combina técnicas tradicionales de ebanistería con un diseño contemporáneo. En CVA no fabricamos
          muebles en serie; creamos compañeros de vida.
        </p>
        <blockquote className="mt-4 border-l-4 border-amber-700 pl-4 italic text-zinc-700">
          &ldquo;Nuestra misión es que cada mueble cuente una historia de calidad, calidez y herencia
          correntina.&rdquo;
        </blockquote>
      </section>

      <section className="mb-10 rounded-lg bg-zinc-50 p-6 text-center">
        <p className="text-sm font-bold uppercase tracking-wide text-amber-700">Nuestra Esencia</p>
        <h2 className="mt-1 text-2xl font-bold">Compromiso Artesano</h2>
        <p className="mx-auto mt-4 max-w-xl italic text-zinc-600">
          &ldquo;En cada pieza que creamos, ponemos el mismo cuidado que pondríamos en un mueble para nuestra
          propia casa&rdquo;
        </p>
        <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <h3 className="font-semibold">Sustentabilidad</h3>
            <p className="text-sm text-zinc-500">Maderas de reforestación y proceso residuo cero.</p>
          </div>
          <div>
            <h3 className="font-semibold">Hecho a Mano</h3>
            <p className="text-sm text-zinc-500">Técnicas de ebanistería tradicional correntina.</p>
          </div>
          <div>
            <h3 className="font-semibold">Calidad Eterna</h3>
            <p className="text-sm text-zinc-500">
              Muebles diseñados para pasar de generación en generación.
            </p>
          </div>
        </div>
      </section>

      <section>
        <p className="text-sm font-bold uppercase tracking-wide text-amber-700">El Factor Humano</p>
        <h2 className="mt-1 mb-6 text-2xl font-bold">Manos Maestras</h2>
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-3">
          <div>
            <h3 className="font-semibold">Acevedo Cesar</h3>
            <p className="text-xs font-bold uppercase text-amber-700">Maestro Ebanista</p>
            <p className="mt-2 text-sm text-zinc-500">
              Más de 25 años transformando la madera en arte. Especialista en tallado a mano.
            </p>
          </div>
          <div>
            <h3 className="font-semibold">Valeria Acevedo</h3>
            <p className="text-xs font-bold uppercase text-amber-700">Diseñadora Industrial</p>
            <p className="mt-2 text-sm text-zinc-500">
              Fusiona lo contemporáneo con lo tradicional para crear piezas que son tendencia.
            </p>
          </div>
          <div>
            <h3 className="font-semibold">Andrés Rojas</h3>
            <p className="text-xs font-bold uppercase text-amber-700">Maestro de Acabados</p>
            <p className="mt-2 text-sm text-zinc-500">
              Dota a cada pieza de un carácter único con técnicas de envejecido natural.
            </p>
          </div>
        </div>
      </section>
    </main>
  );
}
