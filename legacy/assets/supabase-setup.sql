-- Aproavena · esquema de Noticias para Supabase
-- Ejecutar completo en el SQL editor del proyecto.
--
-- IMPORTANTE: después de este archivo ejecuta también "supabase-seguridad.sql".
-- Las políticas de más abajo dejan administrar las noticias a CUALQUIER usuario
-- autenticado, lo que no es seguro si el registro público está habilitado.
-- El segundo archivo las reemplaza por otras limitadas a una lista de admins.

create table if not exists noticias (
  id uuid primary key default gen_random_uuid(),
  title text not null,
  summary text not null,
  body text not null,
  cover_url text,
  published boolean not null default true,
  published_at date not null default current_date,
  created_at timestamptz not null default now()
);

alter table noticias enable row level security;

create policy "Lectura pública de noticias publicadas"
  on noticias for select
  using (published = true);

create policy "Usuarios autenticados leen todo"
  on noticias for select
  to authenticated
  using (true);

create policy "Usuarios autenticados administran noticias"
  on noticias for all
  to authenticated
  using (true)
  with check (true);

-- Storage: crea un bucket público llamado "noticias-covers" desde Storage > New bucket
-- (marca "Public bucket") para alojar las imágenes de portada que se suben desde /Admin.dc.html.
