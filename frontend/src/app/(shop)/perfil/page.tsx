import { redirect } from "next/navigation";
import { apiFetchAuthed } from "@/lib/auth";
import type { Perfil } from "@/lib/api";
import PerfilForm from "@/components/forms/PerfilForm";
import PasswordForm from "@/components/forms/PasswordForm";

export default async function PerfilPage() {
  const perfil = await apiFetchAuthed<Perfil>("/perfil");

  if (perfil === null) {
    redirect("/login");
  }

  return (
    <main className="mx-auto max-w-md px-6 py-12">
      <h1 className="mb-8 text-3xl font-semibold">Mi Perfil</h1>

      <PerfilForm perfil={perfil} />

      <hr className="my-8" />

      <h2 className="mb-4 text-xl font-semibold">Cambiar contraseña</h2>
      <PasswordForm />
    </main>
  );
}
