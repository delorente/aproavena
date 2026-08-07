# Versión anterior (runtime DC + Supabase)

Esta carpeta guarda el sitio tal como estaba antes de migrarlo a PHP + MySQL.
**No se sirve ni se usa**: queda solo como referencia.

## Qué había aquí

- `*.dc.html` — páginas del constructor visual, con plantillas `<x-dc>` y
  lógica en clases `DCLogic`.
- `support.js` — runtime que cargaba React 18 y **Babel standalone** desde
  unpkg y compilaba los componentes en el navegador de cada visitante. Esa
  era la razón principal para migrar: unos megabytes de descarga y una
  compilación en cliente antes de pintar nada.
- `assets/supabase-*.js|sql` — configuración y esquema de Supabase.
- `assets/news-data.js` — noticias de respaldo. Su contenido está ahora en
  `sql/schema.sql` como filas reales de la tabla `noticias`.

## Si alguna vez hay que volver al constructor visual

Los `.dc.html` siguen siendo editables en la herramienta que los generó.
Ten en cuenta que el sitio en producción ya no los lee: cualquier cambio
hecho ahí habría que trasladarlo a los `.php` de la raíz.

## Se puede borrar

Sí. Todo está en el historial de Git (commit «Versión inicial del sitio
AproAvena»), así que eliminar esta carpeta no pierde nada.
