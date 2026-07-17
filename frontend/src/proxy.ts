import { NextRequest, NextResponse } from "next/server";

const AUTH_REQUIRED_PATHS = ["/perfil", "/favoritos", "/pedidos", "/galeria/subir"];
const ADMIN_PREFIX = "/admin";

/**
 * Guard centralizado de rutas. Antes cada página protegida repetía su propio chequeo
 * de cookie + redirect (y "/galeria/subir" ni siquiera lo tenía). Esto evita que una
 * ruta nueva se agregue sin protección por olvido.
 *
 * Es un chequeo de UX, no la autorización real: el JWT nunca se verifica acá (no hay
 * secreto disponible en el edge), solo se decodifica para decidir el redirect. La API
 * PHP siempre vuelve a validar la firma y el rol en cada request — esta capa solo evita
 * que un usuario sin sesión llegue a ver una pantalla que de todos modos va a fallar.
 */
export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const token = request.cookies.get("cva_access_token")?.value;

  const requiresAuth = AUTH_REQUIRED_PATHS.some((p) => pathname.startsWith(p));
  const requiresAdmin = pathname.startsWith(ADMIN_PREFIX);

  if (!requiresAuth && !requiresAdmin) {
    return NextResponse.next();
  }

  if (!token) {
    return NextResponse.redirect(new URL("/login", request.url));
  }

  if (requiresAdmin && !isAdminToken(token)) {
    return NextResponse.redirect(new URL("/", request.url));
  }

  return NextResponse.next();
}

function isAdminToken(token: string): boolean {
  try {
    const payloadB64 = token.split(".")[1];
    const payload = JSON.parse(atob(payloadB64.replace(/-/g, "+").replace(/_/g, "/")));
    return payload.perfil_id === 1;
  } catch {
    return false;
  }
}

// Corre en todas las rutas salvo assets estáticos (patrón recomendado por Next.js);
// qué rutas se protegen de verdad lo decide únicamente AUTH_REQUIRED_PATHS/ADMIN_PREFIX
// arriba, para no tener dos listas separadas que puedan desincronizarse entre sí.
export const config = {
  matcher: ["/((?!api|_next/static|_next/image|.*\\.(?:svg|png|jpg|jpeg|webp|ico)$).*)"],
};
