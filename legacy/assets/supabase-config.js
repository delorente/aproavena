// Configuración de Supabase para el módulo de Noticias.
// 1. Crea un proyecto gratuito en https://supabase.com
// 2. Reemplaza los valores de abajo con tu URL y anon key (Project Settings > API).
// 3. Ejecuta el SQL de "assets/supabase-setup.sql" en el editor SQL de tu proyecto.
// 4. Crea un usuario (Authentication > Users > Add user) para poder entrar a /Admin.dc.html

export const SUPABASE_URL = "https://TU-PROYECTO.supabase.co";
export const SUPABASE_ANON_KEY = "TU-ANON-KEY";

export function isConfigured() {
  return SUPABASE_URL.startsWith("http") && !SUPABASE_URL.includes("TU-PROYECTO");
}

export function getClient() {
  if (!isConfigured() || typeof window === "undefined" || !window.supabase) return null;
  if (!window.__aproavenaSupabaseClient) {
    window.__aproavenaSupabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
  }
  return window.__aproavenaSupabaseClient;
}
