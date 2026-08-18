# Aproavena · instalación y despliegue

Sitio en PHP + MySQL, sin dependencias externas ni proceso de compilación.
Se copia, se configura y funciona.

## El servidor de este proyecto

| Dato | Valor |
| --- | --- |
| Ruta del sitio | `/home/aproavena.cl/public_html` |
| Servidor | `vxsct3508`, familia RHEL/CentOS (Apache en `/etc/httpd`) |
| base_path | Se deduce solo: el sitio va en la raíz del dominio |

El sitio vive **directamente** en `public_html`, no en una subcarpeta.

**Requisitos:** PHP 8.1 o superior (probado en 8.3) con `pdo_mysql`, `fileinfo`
y `mbstring` · MySQL 5.7+ o MariaDB 10.3+ · Apache con `mod_rewrite` y
`AllowOverride All` (para que se apliquen los `.htaccess`).

---

## 1. Base de datos

```sql
CREATE DATABASE aproavena CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aproavena'@'localhost' IDENTIFIED BY 'UNA-CLAVE-LARGA-Y-UNICA';
GRANT SELECT, INSERT, UPDATE, DELETE ON aproavena.* TO 'aproavena'@'localhost';
FLUSH PRIVILEGES;
```

Un usuario propio, no `root`, y sin `DROP` ni `ALTER`: si algún día algo se
cuela por la aplicación, el daño posible queda acotado.

Luego importa el esquema:

```bash
mysql -u aproavena -p aproavena < sql/schema.sql
```

Trae ya cargadas las tres noticias que el sitio mostraba como contenido de
ejemplo, así que arranca con contenido real y no vacío.

---

## 2. Configuración

```bash
cp inc/config.example.php inc/config.php
```

Edita `inc/config.php`:

| Clave | Qué poner |
| --- | --- |
| `db.*` | Los datos del usuario creado arriba |
| `base_path` | Déjalo en `null`: se deduce solo comparando la carpeta del proyecto con el `DocumentRoot`, y funciona igual en la raíz del dominio que en `localhost/proyectos/aproavena/`. Solo ponle un valor fijo detrás de un proxy inverso, donde la ruta pública no coincide con la del disco |
| `mail.to` | Dónde deben llegar los mensajes de contacto |
| `mail.from` | Una casilla **del propio dominio**. Si pones aquí el correo del visitante, SPF/DKIM lo tratan como falsificación y va a spam |
| `debug` | `false` en el VPS. Siempre |

`inc/config.php` está en `.gitignore`: no se sube al repositorio.

---

## 3. Permisos

Solo una carpeta necesita escritura, la de los archivos que se suben desde el
panel:

```bash
cd /home/aproavena.cl/public_html
mkdir -p media/noticias
chown -R apache:apache media/noticias   # en RHEL/CentOS el usuario es apache, no www-data
chmod 755 media/noticias
```

Si `chown apache:apache` da error, mira con qué usuario corre el servidor:
`ps aux | grep httpd | grep -v grep | head -2`

El resto del proyecto puede quedar en solo lectura para el servidor web.

---

## 3b. Límites de subida (confirmado: el VPS viene en 2M)

**Sin esto el cliente no puede subir fotos.** PHP trae `upload_max_filesize`
en 2M, muy por debajo de los 8 MB que acepta el código para imágenes. Una foto
de teléfono pasa de 2 MB sin esfuerzo y el panel respondería «El archivo supera
el tamaño máximo permitido por el servidor».

El proyecto ya incluye un `.user.ini` en la raíz con los valores correctos:

```ini
upload_max_filesize = 20M
post_max_size       = 24M   ; siempre mayor que upload_max_filesize
max_file_uploads    = 20
```

Eso basta si PHP corre como **FPM, CGI o FastCGI**, lo habitual en servidores
con estructura `/home/dominio/public_html`. Tarda hasta 5 minutos en aplicarse
(`user_ini.cache_ttl`). Con **mod_php** el `.user.ini` se ignora: ahí hay que
editar `/etc/php.ini` y reiniciar Apache.

Para saber cuál es tu caso:

```bash
ps aux | grep php-fpm | grep -v grep | head -3   # si sale algo, es FPM
php --ini | head -3                              # php.ini de la consola
```

**Cuidado con cómo lo compruebas.** Este comando lee el `php.ini` de la CLI,
que no tiene por qué coincidir con el del servidor web:

```bash
php -r 'echo ini_get("upload_max_filesize");'    # NO es el valor que importa
```

El valor real es el del web. Para verlo, un archivo temporal en `public_html`:

```bash
echo '<?php phpinfo();' > /home/aproavena.cl/public_html/_ini.php
# ábrelo en el navegador, busca upload_max_filesize, y bórralo enseguida:
rm /home/aproavena.cl/public_html/_ini.php
```

Bórralo apenas termines: `phpinfo()` expone rutas, módulos y configuración del
servidor.

La prueba definitiva es funcional: sube una foto de 4-5 MB a una noticia desde
el panel. Si guarda, está resuelto.

---

## 4. Usuario del panel

```bash
php crear-usuario.php correo@aproavena.cl "Nombre Apellido"
```

Pide la contraseña por teclado, así no queda en el historial del shell. El
script solo corre por consola; por HTTP está bloqueado.

El mismo comando sirve para cambiarle la contraseña a un usuario existente.

---

## 5. Apache

Con el sitio en la raíz de un VirtualHost:

```apache
<VirtualHost *:80>
    ServerName aproavena.cl
    ServerAlias www.aproavena.cl
    DocumentRoot /home/aproavena.cl/public_html

    <Directory /home/aproavena.cl/public_html>
        AllowOverride All      # imprescindible: sin esto los .htaccess se ignoran
        Require all granted
    </Directory>

    ErrorLog  /var/log/httpd/aproavena-error.log
    CustomLog /var/log/httpd/aproavena-access.log combined
</VirtualHost>
```

Después, HTTPS:

```bash
certbot --apache -d aproavena.cl -d www.aproavena.cl
```

Con el certificado ya emitido, descomenta la línea de
`Strict-Transport-Security` en el `.htaccess` de la raíz.

---

## 6. Comprobación

- [ ] La portada carga y muestra tres noticias
- [ ] `/noticias.php` lista y `/noticia.php?id=1` abre el detalle con su PDF
- [ ] `/noticia.php?id=99999` devuelve 404, no un error de PHP
- [ ] `/admin/` redirige a `/admin/login.php`
- [ ] Entras al panel, creas una noticia con portada y aparece en el sitio
- [ ] El formulario de contacto guarda y el mensaje sale en `/admin/mensajes.php`
- [ ] `https://aproavena.cl/inc/config.php` devuelve **403**, no el archivo
- [ ] `https://aproavena.cl/inc/.htaccess` devuelve **403**
- [ ] `https://aproavena.cl/sql/schema.sql` devuelve **403**

Los dos últimos son los que importan: si `AllowOverride` no está en `All`,
los `.htaccess` se ignoran y las credenciales quedan expuestas.

---

## Estructura

```text
index.php  quienes-somos.php  la-avena.php
directorio.php  noticias.php  noticia.php  contacto.php
admin/         panel: login, noticias (CRUD), mensajes
inc/           config, conexión, helpers, auth, subidas, plantillas
assets/        CSS e imágenes del sitio
media/noticias/  portadas y PDF subidos desde el panel
sql/schema.sql  esquema + contenido inicial
legacy/        versión anterior (runtime DC + Supabase), no se sirve
```

## Cómo se resolvió la seguridad

Con Supabase la autorización vivía en la base (políticas RLS). En PHP vive en
el código, así que quedó concentrada en pocos puntos:

- **SQL**: PDO con `ATTR_EMULATE_PREPARES => false`, consultas preparadas
  siempre. No hay concatenación de variables en ninguna consulta.
- **XSS**: `e()` sobre toda salida variable. El cuerpo de las noticias usa
  `nl2br(e($texto))`, que respeta los saltos de línea sin dejar pasar HTML.
- **CSRF**: token de sesión obligatorio en todo POST, comparado con
  `hash_equals`. Cookie con `SameSite=Lax` y `HttpOnly`.
- **Sesión**: `session_regenerate_id(true)` al autenticar, contra fijación de
  sesión. Cierre solo por POST con token.
- **Contraseñas**: `password_hash`/`password_verify` con rehash automático.
  Ante un correo inexistente se verifica igual un hash de relleno, para que el
  tiempo de respuesta no delate qué correos están registrados.
- **Subidas**: el tipo se decide leyendo el contenido con `finfo`, no por la
  extensión ni por lo que declare el navegador. El nombre lo genera el
  servidor. `media/.htaccess` impide que Apache ejecute PHP en esa carpeta.
- **Fuerza bruta**: 5 intentos de login y 60 segundos de espera.
