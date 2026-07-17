export interface Perfil {
  id_usuario: number;
  nombre: string;
  apellido: string;
  email: string;
  usuario: string;
  perfil_id: number;
  imagen: string | null;
}

export interface LoginResponse {
  access_token: string;
  refresh_token: string;
  expires_in: number;
  user: Record<string, unknown>;
}
