# CV-as-Code — cómo actualizar el currículum

El CV de https://pascualmg.dev/cv **no se edita como un documento**: es una
*vista* de unos datos. Para cambiar el CV se editan unos ficheros JSON y el
CV se regenera solo. El PDF lo produce el navegador al imprimir (botón
"Descargar / Imprimir PDF" en la página, o el botón del portfolio).

## Qué hay que cambiar (los 3 JSON)

Viven en:

```
src/ddd/Infrastructure/webserver/html/cv-data/
├── timeline.json   ← experiencia laboral (lo que más se toca)
├── contact.json    ← nombre, título, resumen, email, links, ciudad
└── skills.json     ← competencias, tecnologías, formación, idiomas
```

### timeline.json — añadir o editar experiencia

Un array de empresas, la más reciente primero. Cada empresa:

```json
{
  "company": "Nombre Empresa",
  "position": "Tu puesto",
  "startDate": "2024-07-01",
  "endDate": null,                  // null = "Actualidad"
  "projects": [
    {
      "name": "Nombre del proyecto",
      "role": "Tu rol en el proyecto",
      "highlights": [
        "Logro o responsabilidad, una frase por línea"
      ],
      "technologies": ["PHP", "Symfony"],   // chips de tecnología
      "achievements": ["Logros destacados (se muestran en negrita)"]
    }
  ]
}
```

Para **añadir un trabajo nuevo**: copia un bloque de empresa, cámbialo y
ponlo arriba del array. Las fechas son `AAAA-MM-DD`; `endDate: null` se
imprime como "Actualidad".

### contact.json — datos de cabecera

```json
{
  "name": "Pascual Muñoz Galián",
  "title": "PHP Software Engineer",
  "summary": "Párrafo de presentación.",
  "location": "Ciudad, Provincia (País)",
  "email": "info@pascualmg.dev",
  "phone": "+34 ...",
  "website": "pascualmg.dev",
  "links": [ { "name": "LinkedIn", "url": "..." } ]
}
```

> La **dirección postal exacta no se pone aquí** (el repo es público). Si
> algún día hace falta un dato sensible solo para el CV impreso, va en
> `contact.local.json` (mismo formato, ignorado por git — ver `.gitignore`).

### skills.json — competencias, stack, formación, idiomas

`softSkills` (lista de frases), `technologies` (lista de nombres → chips del
stack), `education` (`title`, `detail`, `period`), `languages` (`name`,
`level`).

## Cómo subir el cambio

1. Editar el/los JSON.
2. Validar que el JSON es correcto (una coma de más lo rompe):
   `php -r 'json_decode(file_get_contents("ruta.json"), false, 512, JSON_THROW_ON_ERROR); echo "OK\n";'`
3. `git add` + `git commit` + `git push`.
4. Desplegar (en el servidor): `git pull` y reiniciar el servicio del blog.

Tras el deploy, https://pascualmg.dev/cv refleja el cambio al instante (los
JSON se leen en cada carga). No hay que tocar HTML ni CSS.

## Generar el PDF para enviarlo

1. Abrir https://pascualmg.dev/cv (o el botón "Descargar mi CV (PDF)" del
   portfolio).
2. Botón "Descargar / Imprimir PDF" → diálogo de impresión del navegador.
3. En el diálogo: **márgenes "Ninguno"** y **desactivar "Encabezados y pies
   de página"**. Guardar como PDF.

Salen 2 páginas A4 con cortes limpios. Si se añade mucha experiencia y
crece a 3 páginas, basta con afinar espaciados en `cv.html` (`.job`,
`.project`) — pero el contenido manda.

## Cómo está montado (para quien toque el código)

- `GET /cv` → `CvController` sirve `webserver/html/cv.html` (HTML plano, sin
  Web Components: las reglas `@media print` no atraviesan bien el shadow DOM).
- `cv.html` hace `fetch` de los 3 JSON, monta el DOM con JS vanilla e
  imprime con `window.print()`. La generación del PDF es **del cliente**; el
  servidor solo sirve estáticos (cero navegador headless en producción).
- La foto: `webserver/html/img/pascual-cv.jpg`.
