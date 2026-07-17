import ContactoForm from "@/components/forms/ContactoForm";

export const metadata = { title: "Contacto — CVA Muebles" };

export default function ContactoPage() {
  return (
    <main className="mx-auto max-w-xl px-6 py-12">
      <header className="mb-8 text-center">
        <h1 className="text-3xl font-bold">Escribinos tu Consulta</h1>
        <p className="mt-2 text-zinc-600">
          Contanos qué tenés en mente y te responderemos a la brevedad con una propuesta personalizada.
        </p>
      </header>

      <ContactoForm />
    </main>
  );
}
