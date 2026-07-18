"use client";

import Image from "next/image";
import Link from "next/link";
import { useEffect, useState } from "react";

const SLIDES = [
  {
    image: "/img/hero/taller.jpg",
    align: "center" as const,
    animate: "animate-up",
    badge: "Mueblería de Autor",
    title: (
      <>
        La Esencia de la <br />
        <span className="text-cva-gold">Madera Noble</span>
      </>
    ),
    text: "Transformamos maderas seleccionadas en piezas únicas que respiran historia y elegancia.",
    actions: (
      <>
        <Link href="/" className="btn-vivid">
          Ver Catálogo
        </Link>
        <Link href="/quienes-somos" className="btn-outline-light-hero">
          Nuestro Taller
        </Link>
      </>
    ),
  },
  {
    image: "/img/hero/muebles-22.jpeg",
    align: "start" as const,
    animate: "animate-left",
    title: (
      <>
        Oficio que <br />
        Perdura
      </>
    ),
    text: "Cada veta cuenta una historia de paciencia, técnica y amor por lo artesanal.",
    actions: (
      <Link href="/" className="btn-gold">
        Descubrir Más
      </Link>
    ),
    borderClass: "left-align",
  },
  {
    image: "/img/hero/muebles-69.jpeg",
    align: "end" as const,
    animate: "animate-right",
    title: (
      <>
        Diseño para <br />
        Toda la Vida
      </>
    ),
    text: "Creamos el corazón de tu hogar con muebles que trascienden generaciones.",
    actions: (
      <Link href="/contacto" className="btn-vivid">
        Pedir Presupuesto
      </Link>
    ),
    borderClass: "right-align",
  },
];

export default function Hero() {
  const [active, setActive] = useState(0);

  useEffect(() => {
    const id = setInterval(() => setActive((i) => (i + 1) % SLIDES.length), 6000);
    return () => clearInterval(id);
  }, []);

  const justify = {
    center: "justify-center text-center",
    start: "justify-start text-left lg:pl-[8%]",
    end: "justify-end text-right lg:pr-[8%]",
  };

  return (
    <div className="relative h-[70vh] min-h-[500px] overflow-hidden lg:h-[85vh] lg:min-h-[600px]">
      {SLIDES.map((slide, i) => (
        <div
          key={slide.image}
          className={`absolute inset-0 h-full w-full transition-opacity duration-700 ${
            i === active ? "opacity-100" : "pointer-events-none opacity-0"
          }`}
        >
          <div className="hero-overlay-artisan" />
          <Image
            src={slide.image}
            alt=""
            fill
            priority={i === 0}
            sizes="100vw"
            className={`object-cover ${i === active ? "zoom-animation" : ""}`}
          />
          <div className={`relative z-10 flex h-full items-center px-6 ${justify[slide.align]}`}>
            <div
              className={`glass-caption ${slide.borderClass ?? ""} ${i === active ? slide.animate : ""}`}
            >
              {slide.badge && (
                <span className="mb-3 inline-block rounded-full bg-cva-gold px-3 py-2 text-xs font-bold tracking-widest text-white uppercase">
                  {slide.badge}
                </span>
              )}
              <h1 className="font-heading mb-3 text-3xl font-bold lg:text-6xl">{slide.title}</h1>
              <p className="mx-auto mb-4 max-w-xl text-base opacity-90 lg:text-lg">{slide.text}</p>
              <div className="flex flex-wrap items-center gap-3">{slide.actions}</div>
            </div>
          </div>
        </div>
      ))}

      <button
        aria-label="Anterior"
        onClick={() => setActive((i) => (i - 1 + SLIDES.length) % SLIDES.length)}
        className="absolute top-1/2 left-4 z-20 -translate-y-1/2"
      >
        <span className="control-circle">←</span>
      </button>
      <button
        aria-label="Siguiente"
        onClick={() => setActive((i) => (i + 1) % SLIDES.length)}
        className="absolute top-1/2 right-4 z-20 -translate-y-1/2"
      >
        <span className="control-circle">→</span>
      </button>

      <div className="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 gap-2">
        {SLIDES.map((slide, i) => (
          <button
            key={slide.image}
            aria-label={`Ir al slide ${i + 1}`}
            onClick={() => setActive(i)}
            className={`h-1.5 w-8 rounded-full transition ${i === active ? "bg-cva-gold" : "bg-white/40"}`}
          />
        ))}
      </div>
    </div>
  );
}
