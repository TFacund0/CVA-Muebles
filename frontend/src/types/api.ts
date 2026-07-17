/** Envelope de respuesta estándar de la API PHP (backend/app/Controllers/Api/BaseApiController.php). */
export interface ApiEnvelope<T> {
  status: "success" | "error";
  data: T | null;
  message: string | null;
}
