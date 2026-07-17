import { redirect } from "next/navigation";
import AdminSidebar from "@/components/admin/AdminSidebar";
import { apiFetchAuthed } from "@/lib/auth";
import type { Perfil } from "@/lib/api";

export default async function AdminLayout({ children }: { children: React.ReactNode }) {
  const perfil = await apiFetchAuthed<Perfil>("/auth/me");

  if (perfil === null) {
    redirect("/login");
  }

  if (perfil.perfil_id !== 1) {
    redirect("/");
  }

  return (
    <div className="flex flex-1">
      <AdminSidebar />
      <main className="flex-1 p-6">{children}</main>
    </div>
  );
}
