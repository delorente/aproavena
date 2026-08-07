-- Aproavena · endurecimiento de permisos (RLS)
-- ---------------------------------------------------------------------------
-- Reemplaza las políticas permisivas de "supabase-setup.sql", que daban control
-- total sobre las noticias a CUALQUIER usuario autenticado. Con este script solo
-- pueden administrar quienes estén en la tabla "admins".
--
-- CÓMO USARLO
--   1. Abre el SQL editor de tu proyecto en Supabase.
--   2. Pega este archivo completo.
--   3. Antes de ejecutar, edita el INSERT del PASO 3 con tu correo real.
--      (No hace falta guardar tu correo en el repo: escríbelo solo aquí.)
--   4. Ejecuta. Es idempotente: puedes correrlo más de una vez sin romper nada.
--
-- Además, en el panel de Supabase: Authentication > Sign In / Providers > Email
-- y desactiva "Allow new users to sign up". Este script ya evita que un registro
-- anónimo pueda tocar las noticias, pero cerrar el alta pública evita también que
-- cualquiera te llene la tabla de usuarios.
-- ---------------------------------------------------------------------------


-- PASO 1 · Tabla de administradores autorizados ------------------------------
-- Lleva RLS activado y CERO políticas a propósito: así resulta invisible desde
-- la API pública (anon y authenticated). Solo se administra desde este SQL
-- editor, que corre como service_role y se salta RLS.

create table if not exists public.admins (
  email      text primary key,
  nombre     text,
  created_at timestamptz not null default now()
);

alter table public.admins enable row level security;


-- PASO 2 · Función que responde "¿el usuario actual es admin?" ---------------
-- security definer porque el propio usuario no puede leer la tabla admins.

create or replace function public.is_admin()
returns boolean
language sql
stable
security definer
set search_path = public
as $$
  select exists (
    select 1
    from public.admins
    where lower(email) = lower(auth.jwt() ->> 'email')
  );
$$;


-- PASO 3 · Da de alta a los administradores ---------------------------------
-- EDITA ESTA LÍNEA con tu correo (el mismo con el que inicias sesión en el
-- panel). Agrega una fila por cada socio que deba administrar noticias.
-- Ojo: el correo debe corresponder a un usuario ya creado en Authentication.

insert into public.admins (email, nombre) values
  ('TU-CORREO@ejemplo.com', 'Administrador')
  -- , ('otro.socio@ejemplo.com', 'Nombre del socio')
on conflict (email) do nothing;


-- PASO 4 · Políticas de la tabla "noticias" ---------------------------------

drop policy if exists "Lectura pública de noticias publicadas"   on public.noticias;
drop policy if exists "Usuarios autenticados leen todo"          on public.noticias;
drop policy if exists "Usuarios autenticados administran noticias" on public.noticias;
drop policy if exists "noticias_select_publicas" on public.noticias;
drop policy if exists "noticias_select_admin"    on public.noticias;
drop policy if exists "noticias_insert_admin"    on public.noticias;
drop policy if exists "noticias_update_admin"    on public.noticias;
drop policy if exists "noticias_delete_admin"    on public.noticias;

alter table public.noticias enable row level security;

-- Cualquier visitante lee las noticias publicadas (el sitio es público).
create policy "noticias_select_publicas"
  on public.noticias for select
  to anon, authenticated
  using (published = true);

-- Los admins además ven los borradores. Las políticas SELECT se suman (OR),
-- así que un admin ve todo y un visitante solo lo publicado.
create policy "noticias_select_admin"
  on public.noticias for select
  to authenticated
  using (public.is_admin());

create policy "noticias_insert_admin"
  on public.noticias for insert
  to authenticated
  with check (public.is_admin());

create policy "noticias_update_admin"
  on public.noticias for update
  to authenticated
  using (public.is_admin())
  with check (public.is_admin());

create policy "noticias_delete_admin"
  on public.noticias for delete
  to authenticated
  using (public.is_admin());


-- PASO 5 · Políticas del bucket de portadas ---------------------------------
-- El bucket "noticias-covers" es público de lectura, pero solo un admin sube,
-- reemplaza o borra imágenes. El UPDATE es necesario porque el panel sube con
-- upsert: true (ver Admin.dc.html > handleFile).
--
-- Si tu proyecto rechaza estas sentencias por permisos sobre storage.objects,
-- crea las mismas cuatro reglas desde Storage > Policies en el panel web.

drop policy if exists "covers_public_read"  on storage.objects;
drop policy if exists "covers_admin_insert" on storage.objects;
drop policy if exists "covers_admin_update" on storage.objects;
drop policy if exists "covers_admin_delete" on storage.objects;

create policy "covers_public_read"
  on storage.objects for select
  to anon, authenticated
  using (bucket_id = 'noticias-covers');

create policy "covers_admin_insert"
  on storage.objects for insert
  to authenticated
  with check (bucket_id = 'noticias-covers' and public.is_admin());

create policy "covers_admin_update"
  on storage.objects for update
  to authenticated
  using      (bucket_id = 'noticias-covers' and public.is_admin())
  with check (bucket_id = 'noticias-covers' and public.is_admin());

create policy "covers_admin_delete"
  on storage.objects for delete
  to authenticated
  using (bucket_id = 'noticias-covers' and public.is_admin());


-- PASO 6 · Comprobación ------------------------------------------------------
-- Debe listar las 5 políticas de noticias y las 4 de storage.

select tablename, policyname, cmd
from pg_policies
where (schemaname = 'public' and tablename in ('noticias', 'admins'))
   or (schemaname = 'storage' and policyname like 'covers_%')
order by tablename, policyname;

-- Prueba de fuego, ya con el sitio andando:
--   1. Inicia sesión en /Admin.dc.html con tu correo → debes poder crear y borrar.
--   2. Crea un usuario de prueba que NO esté en "admins" e inicia sesión con él:
--      el panel debe aparecer vacío y cualquier intento de guardar debe fallar.
