# 🚀 Guías de Prácticas Laravel - TaskBoard: Pasarela de Pagos

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Postman](https://img.shields.io/badge/Postman-FF6C37?style=for-the-badge&logo=postman&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?style=for-the-badge&logo=git&logoColor=white)
![GitHub](https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white)

---

<p align="center">
  <img src="https://res.cloudinary.com/bcwlyire/image/upload/v1785118882/NUEVO-LOGO-UPED-A-COLOR-scaled_szoqws.jpg" alt="Logo UPED" width="300"/>
</p>

## 🏛️ Información Académica

- **Institución:** Universidad Pedagógica de El Salvador *Luis Alonso Aparicio*
- **Facultad:** Ingeniería
- **Asignatura:** Integración de Sistemas
- **Ciclo:** 02-2026
- **Docente:** Ing. Oscar Contreras
- **Proyecto Integrador:** TaskBoard - Pasarela de Pagos

---

## 📌 Descripción del Proyecto

Este repositorio contiene el trabajo de la **Semana 14** del proyecto **TaskBoard**, una plataforma de **Pasarela de Pagos** desarrollada en el framework **Laravel 12**.

La Semana 13 construyó la segunda puerta del sistema: una API que responde JSON bien formado, con el contrato controlado por API Resources. Y la dejó **sin cerradura**: cualquiera con la dirección del servidor podía leer los cobros de todos los comercios, crear cobros falsos en el negocio de otro y borrar registros, sin decir nunca quién era. Esta semana se le pone la llave.

Son **dos guías y una sola entrega**, como quedó indicado en clase:

**Guía N.° 13 — Semana 14 · Jueves: Autenticación de API con Laravel Sanctum**

1. Comprobar con un experimento que la API de la Semana 13 estaba abierta a cualquiera.
2. Preparar Sanctum y agregar `HasApiTokens` al modelo `User`.
3. Crear `POST /api/login`, que verifica credenciales y entrega un **token personal**.
4. Proteger todas las rutas con `auth:sanctum` (sin token: **401**).
5. Filtrar comercios y transacciones por el **dueño del token** (lo ajeno: **404**).
6. Revocar el token con `POST /api/logout` y limitarle la vida útil.
7. El reto: `GET /api/tokens`, la pantalla de *dispositivos con sesión iniciada*.

**Guía N.° 14 — Semana 14 · Viernes: Probar, depurar y consumir la API protegida**

1. Organizar una colección con **dos identidades** (Ana y Carlos) y tokens guardados por script.
2. Escribir **pruebas automáticas** que confirmen 200, 401, 404 y 422.
3. Romper la API a propósito y depurarla con el método de 5 pasos.
4. Inspeccionar los tokens desde `tinker`.
5. Consumir la API desde **PHP** (`Http::withToken`) y desde una **página web** (`fetch`).
6. El reto: convertir esa página en un panel con conteo de cobros y botón de salida.

> 🔑 **La idea que sostiene la semana.** El token es como la pulsera de un evento: uno se identifica una vez en la entrada y después solo muestra la pulsera. Quien la tenga entra como si fuera uno; por eso se cuida como una contraseña y se corta al salir.

> 📄 Las respuestas a las preguntas de análisis, las matrices de verificación de los dos días y las dos bitácoras están en **[RESPUESTAS_SEMANA14.md](RESPUESTAS_SEMANA14.md)**.

### 📐 Una aclaración sobre los números de la guía

La guía usa el **comercio 2** como ejemplo de comercio ajeno a Ana. En este proyecto el seeder reparte distinto: **Ana tiene los comercios 1 y 2, y Carlos los comercios 3 y 4**. Entonces, en todas las pruebas de este repositorio, el comercio ajeno a Ana es el **3** y la transacción ajena es la **4**. Cambia el número, no la idea.

---

## 📁 Estructura del Proyecto

Los archivos marcados son los que cambian esta semana. La parte web (vistas, login de navegador) **no se toca**:

```text
taskboard/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthApiController.php             # NUEVO  login, yo, logout, tokens, revocarToken
│   │   │   │   └── TransaccionApiController.php      # MODIFICADO  filtra y autoriza por dueño
│   │   │   ├── AuthController.php                    # web, sin cambios
│   │   │   ├── ComercioController.php                # web, sin cambios
│   │   │   └── TransaccionController.php             # web, sin cambios
│   │   ├── Requests/
│   │   │   └── GuardarTransaccionRequest.php         # MODIFICADO  se fue el if de "sin usuario"
│   │   └── Resources/
│   │       ├── ComercioResource.php                  # sin cambios
│   │       └── TransaccionResource.php               # MODIFICADO  + comercio_id
│   └── Models/
│       └── User.php                                  # MODIFICADO  + HasApiTokens, + transacciones()
├── bootstrap/
│   └── app.php                                       # MODIFICADO  el 404 de la API, en JSON limpio
├── config/
│   └── sanctum.php                                   # MODIFICADO  expiration = 60 * 24
├── database/
│   └── migrations/
│       └── ..._create_personal_access_tokens_table.php   # ya existía, desde install:api
├── public/
│   └── cliente.html                                  # NUEVO  cliente web con fetch()
├── routes/
│   ├── api.php                                       # MODIFICADO  login público + grupo auth:sanctum
│   └── web.php                                       # sin cambios
├── pruebas-api.http                                  # MODIFICADO  23 peticiones con token
├── TaskBoard-API-Semana14.postman_collection.json    # NUEVO  4 carpetas, 61 comprobaciones
└── RESPUESTAS_SEMANA14.md                            # NUEVO  respuestas y bitácoras
```

---

## ⚙️ Puesta en marcha

El proyecto corre sobre **XAMPP** (Apache + MySQL) con la base de datos `taskboard_14`.

### 1. Crear la base de datos

```sql
CREATE DATABASE taskboard_14 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Preparar el entorno

```bash
composer install
cp .env.example .env
php artisan key:generate
```

| Variable | Valor |
|---|---|
| `DB_DATABASE` | `taskboard_14` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | *(vacío, usuario por defecto de XAMPP)* |
| `SESSION_DRIVER` | `database` |
| `SESSION_COOKIE` | `taskboard_session` |

### 3. Crear las tablas y los datos de ejemplo

```bash
php artisan migrate:fresh --seed
php artisan serve
```

> 💡 Después de un `migrate:fresh --seed` **los tokens anteriores dejan de existir**, porque la tabla `personal_access_tokens` se vacía con todo lo demás. Hay que volver a correr los dos logins de la colección para llenar las variables.

### 4. Usuarios y datos de prueba

Los mismos de la Semana 12, y ahora la API **sí los necesita**: es con ellos que se pide el token.

| Usuario | Correo | Contraseña | Comercios |
|---|---|---|---|
| Ana López | `ana@taskboard.test` | `password123` | 1 Café Amanecer, 2 Pupusería Doña Marta |
| Carlos Pérez | `carlos@taskboard.test` | `password123` | 3 Ferretería El Tornillo, 4 Librería La Página |

---

## 🔌 Endpoints de la API

Todas las direcciones llevan el prefijo `/api`, y **todas las peticiones deben enviar `Accept: application/json`**. Sin ese encabezado, Laravel supone que del otro lado hay un navegador y responde una redirección a la pantalla de login web en vez de un 401 en JSON.

### Pública

| Verbo | URI | Acción | Respuesta |
|---|---|---|---|
| POST | `/api/login` | Entrega un token personal | **201** + `token` y `tipo`; **422** si las credenciales no coinciden; **429** al sexto intento en un minuto |

### Protegidas — exigen `Authorization: Bearer <token>`

| Verbo | URI | Acción | Respuesta |
|---|---|---|---|
| GET | `/api/yo` | Quién es el dueño del token | 200 + id, name, email |
| POST | `/api/logout` | Revoca el token usado en esta petición | **204** sin cuerpo |
| GET | `/api/tokens` | Sesiones abiertas del usuario *(reto)* | 200 + `data`, **sin** la columna `token` |
| DELETE | `/api/tokens/{id}` | Cierra la sesión de un dispositivo *(reto)* | **204**; **404** si el token no es suyo |
| GET | `/api/comercios` | Los comercios **del dueño del token** | 200 + `data`, **sin** `transacciones` |
| GET | `/api/comercios/{id}` | Un comercio propio | 200 + `data` **con** `transacciones`; **404** si no existe o es ajeno |
| GET | `/api/transacciones` | Las transacciones de sus comercios | 200 + `data` |
| GET | `/api/transacciones?estado=Completada` | Listado filtrado | 200 + `data`, vacío si no hay coincidencias |
| GET | `/api/transacciones/{id}` | Una transacción propia | 200 + `data`; **404** si no existe o es ajena |
| POST | `/api/transacciones` | Crear en un comercio propio | **201** + el objeto creado; **422** si el comercio no es suyo |
| PUT/PATCH | `/api/transacciones/{id}` | Actualizar | 200; **404** si es ajena |
| DELETE | `/api/transacciones/{id}` | Eliminar | **204** sin cuerpo; **404** si es ajena |

Son **12 rutas**: una pública y **once dentro de `auth:sanctum`**. Se comprueba con `php artisan route:list --path=api`.

### Los tres códigos que responden tres preguntas distintas

| Código | Pregunta | Cuándo sale |
|---|---|---|
| **401** | ¿Sé quién eres? → No | Sin token, token mal escrito, vencido o revocado |
| **403** | ¿Puedes hacer esto? → No | **No se usa acá**, y es a propósito (ver *Decisiones de diseño*) |
| **404** | ¿Existe para ti? → No | Ana pide el comercio 3, o una transacción de Carlos |

### Ejemplo de respuesta

`POST /api/login`

```json
{
  "token": "12|XaX6ePOHQ8ZEoZnruyY3exskNWahN8s1qnDs1ZF3",
  "tipo": "Bearer",
  "usuario": { "id": 1, "name": "Ana López", "email": "ana@taskboard.test" }
}
```

`GET /api/tokens` *(el reto del jueves)*

```json
{
  "data": [
    { "id": 5, "dispositivo": "app-movil-ana", "ultimo_uso": null,
      "creado": "2026-11-02T18:00:41+00:00", "es_el_actual": false },
    { "id": 4, "dispositivo": "laptop-ana", "ultimo_uso": "2026-11-02T18:00:41+00:00",
      "creado": "2026-11-02T18:00:41+00:00", "es_el_actual": true }
  ]
}
```

---

## 🧪 Cómo probar la API

En el repositorio van **las dos colecciones**, con el mismo recorrido.

### Opción A — Postman (o Insomnia)

`TaskBoard-API-Semana14.postman_collection.json`, en formato **Postman v2.1**, que **Insomnia también importa**.

- **Postman:** *Import* → arrastrar el archivo.
- **Insomnia:** *Import* → *From File*.

Organizada en cuatro carpetas:

```text
TaskBoard API - Semana 14 (Sanctum)
├── 0. Públicas     los 3 logins + un segundo dispositivo de Ana
├── 1. Ana          Auth de carpeta: Bearer {{tokenAna}}      (13 peticiones)
├── 2. Carlos       Auth de carpeta: Bearer {{tokenCarlos}}   (3 peticiones)
└── 3. Sin token    Auth: No Auth                             (2 peticiones)
```

**Hay que correr primero la carpeta `0. Públicas`**: ahí se llenan las variables. El token no se pega a mano en ninguna petición, lo guarda un script:

```js
// Scripts → Post-response del login
pm.collectionVariables.set('tokenAna', pm.response.json().token);
```

y la carpeta entera lleva *Authorization → Bearer Token → `{{tokenAna}}`*, con las peticiones de adentro en modo **heredar**.

> 🔐 Las variables `tokenAna` y `tokenCarlos` se suben **vacías**. Un archivo de pruebas con un token real adentro es una llave publicada.

> ⏱️ `/api/login` tiene `throttle:5,1`. La colección completa hace **4 logins**, así que correrla dos veces dentro del mismo minuto devuelve **429** en los últimos. No es un error: es la defensa funcionando.

> 🔁 Para la petición **3.2** (el experimento del `Accept`) hay que **apagar** *Settings → General → Automatically follow redirects*; si está encendida, Postman sigue la redirección y no se ve el `302`.

### Opción B — REST Client en VS Code

`pruebas-api.http`. Instalar la extensión **REST Client** (Huachao Mao), abrir el archivo y pulsar *Send Request* sobre la línea del método. **El orden importa:** las peticiones 1 y 2 son los logins, y de su respuesta salen los tokens que usan todas las demás.

```http
# @name loginAna
POST {{host}}/login
...
###
@tokenAna = {{loginAna.response.body.token}}
```

### Opción C — El cliente web

Con el servidor corriendo, abrir **<http://127.0.0.1:8000/cliente.html>**. Es una página que hace login contra la API y muestra los comercios del usuario con su conteo de cobros completados. En *F12 → Red* se ve la petición saliendo con el encabezado `Authorization`.

---

## 🧠 Decisiones de diseño

### El token no reemplaza a la sesión: resuelve otro problema

| | Web (Semana 12) | API (Semana 14) |
|---|---|---|
| Quién recuerda | El **servidor**, en la sesión | **Nadie**: cada petición llega identificada sola |
| Qué viaja | Una **cookie**, que el navegador reenvía solo | Un **token**, que el cliente manda a mano en un encabezado |
| Cerrar sesión | Destruir la sesión y regenerar el CSRF | **Borrar la fila del token** |
| Quién puede usarlo | Solo un navegador | Cualquier programa: app móvil, script, otro servidor |

Una aplicación móvil no puede usar la sesión del navegador porque la sesión no es del usuario: es **del navegador**. La cookie vive dentro de ese programa, atada a su dominio, y ni otro navegador en la misma computadora la ve.

### `404` y no `403` para lo ajeno

```php
abort_unless($comercio->user_id === $request->user()->id, 404);
```

Un `403` diría *"existe, pero no es tuyo"*, y eso **ya es información**: con ids correlativos se podría recorrer la tabla contando cuáles contestan 403 para saber cuántos comercios hay en el sistema y cuáles son de otros. El `404` no distingue entre "no existe" y "no es tuyo", y para quien pregunta son exactamente lo mismo.

### El filtro arranca en el usuario, no en el modelo

```php
// Antes:  Comercio::all()                 ->  todos los de la base
// Ahora:  $request->user()->comercios()   ->  los de quien pregunta
```

La relación `hasMany` ya trae el `where user_id` armado por Eloquent, así que el filtro **no se escribe a mano y no se puede olvidar en una rama del código**. Y como una transacción no tiene columna `user_id` (su dueño es el dueño de su comercio), el listado viaja por `hasManyThrough`: `user → comercios → transacciones`.

### El Form Request perdió su `if`, y eso mide lo que cambió

La semana pasada esta regla tuvo que partirse en dos, porque por la API `$this->user()` era `null`:

```php
// Semana 13
'comercio_id' => ['required', $this->user()
    ? Rule::exists('comercios', 'id')->where('user_id', $this->user()->id)
    : Rule::exists('comercios', 'id')],

// Semana 14
'comercio_id' => ['required',
    Rule::exists('comercios', 'id')->where('user_id', $this->user()->id)],
```

Dentro de `auth:sanctum` **siempre** hay usuario, así que la rama insegura desaparece del archivo. Es el único cambio de seguridad de la semana que se hizo **borrando** código: `store()` no necesitó ni una línea nueva, porque la regla correcta estaba escrita desde la Semana 12 y lo que le faltaba era el usuario.

### El token se guarda como una contraseña

```text
Lo que entrega el login:   12|XaX6ePOHQ8ZEoZnruyY3exsk...
Lo que guarda la tabla:    e38f31ede2b6365459cfef796...   (hash)
```

El texto en claro existe **solo en la respuesta del login**. Ni con acceso a la base de datos se puede recuperar el token de alguien; lo único que se puede hacer es borrarlo. Por eso `GET /api/tokens` lleva la lista de columnas escrita a mano y la columna `token` **no está**: publicar el hash no entregaría la llave, pero sí el material para intentar adivinarla sin límite y fuera de la vista del servidor.

### El 404 de la API, contestado como lo contestaría una API

Esto no lo pedía la guía. Con `APP_DEBUG=true`, un `abort(404)` respondía el código correcto pero con cientos de líneas: el nombre de la excepción, la **ruta absoluta de los archivos en el disco** y toda la pila de llamadas. Son dos problemas: la respuesta dejaba de cumplir el contrato (un cliente que lea `message` recibía una cadena vacía) y publicaba un mapa del servidor. En `bootstrap/app.php`:

```php
$exceptions->render(function (NotFoundHttpException $e, Request $request) {
    if ($request->is('api/*')) {
        return response()->json(['message' => 'No encontramos ese recurso en tu cuenta.'], 404);
    }
});
```

La parte web sigue mostrando el trace completo, que ahí sí se quiere mientras se programa: lo lee una persona, no un programa.

---

## ✅ Verificación

Las **61 comprobaciones** de la colección se ejecutaron contra el servidor y todas quedaron en verde. El detalle de las dos matrices está en [RESPUESTAS_SEMANA14.md](RESPUESTAS_SEMANA14.md).

```text
== 0. Públicas ==
OK  Login Ana                                   201 + token
OK  Login Carlos                                201 + token
OK  Login con clave incorrecta                  422
OK  Login Ana desde un segundo dispositivo      201 + token distinto
== 1. Ana ==
OK  1.1  Yo                                     200 · Ana López
OK  1.2  Mis comercios                          200 · exactamente 2, ids [1, 2]
OK  1.3  Un comercio de Carlos                  404
OK  1.4  Mis transacciones                      200 · solo de los comercios 1 y 2
OK  1.5  Una transacción de Carlos              404
OK  1.6  Crear en mi comercio                   201 · nace en Iniciada
OK  1.7  Crear en un comercio de Carlos         422 · errors.comercio_id
OK  1.8  Mis cobros Completados                 200 · filtro respetado
OK  1.9  Borrar la transacción que creé         204 · sin cuerpo
OK  1.10 Mis sesiones abiertas (reto)           200 · sin la columna token
OK  1.11 Cerrar la sesión del móvil (reto)      204
OK  1.12 Logout                                 204
OK  1.13 Yo con el token ya revocado            401
== 2. Carlos ==
OK  2.1  Yo                                     200 · Carlos Pérez
OK  2.2  Mis comercios                          200 · ids [3, 4]
OK  2.3  Borrar una transacción de Ana          404 · no borró nada
== 3. Sin token ==
OK  3.1  Comercios con Accept                   401 · {"message":"Unauthenticated."}
OK  3.2  Comercios sin Accept                   302 · HTML, no JSON

61 comprobaciones en verde | 0 en rojo
```

### Los cuatro errores del laboratorio de depuración

Provocados uno por uno y reparados antes de pasar al siguiente:

| # | Cómo se provocó | Código | Qué decía |
|---|---|---|---|
| A | Token **sin** la palabra `Bearer` | **401** | `{"message":"Unauthenticated."}`. Nada en el log |
| B | Quitar el encabezado `Accept` sin token | **302** | HTML y `Location: /login`. Nada en el log |
| C | Comentar `use HasApiTokens` en `User.php` | **500** | `Call to undefined method App\Models\User::createToken()` |
| D | `Comercio::query()` en vez de `$request->user()->comercios()` | **200** | **Nada.** Ni error, ni log. Solo que devolvía **4 comercios en vez de 2** |

> ⚠️ **El error D es el importante.** Los otros tres gritan: cambian el código de estado y se ven solos. El D responde 200, con la estructura correcta y los campos correctos, y de los cuatro es el **único que filtra datos de otra persona**. La única prueba que lo marca en rojo es la que cuenta: `expect(d.map(c => c.id)).to.eql([1, 2])`. Una prueba automática que solo revisa el código de estado da una falsa sensación de seguridad.

### Dos ajustes que hicieron falta

**La columna ambigua en el listado de transacciones.** Al pasar el filtro a `hasManyThrough`, el `latest()` a secas rompía con *"Column 'created_at' in order clause is ambiguous"*: el JOIN trae `comercios`, que también tiene esa columna. Se resuelve nombrando la tabla:

```php
->latest('transacciones.created_at')
```

**El orden de los tokens.** `latest()` en `GET /api/tokens` dejaba el empate en manos de MySQL cuando dos tokens se emitían en el mismo segundo, y la lista salía distinta en cada corrida. Se cambió a `orderByDesc('id')`, que siempre crece.

---

## 📚 Guías anteriores

- [RESPUESTAS_SEMANA12.md](RESPUESTAS_SEMANA12.md) — cookies, sesiones y autenticación web
- [RESPUESTAS_SEMANA13.md](RESPUESTAS_SEMANA13.md) — servicios web REST, JSON y API Resources
