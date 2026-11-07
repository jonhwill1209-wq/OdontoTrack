# Entrega P5 de OdontoTrack

## Alcance implementado
Se completó la gestión de expediente clínico sobre la rama develop descargada del repositorio del equipo. La entrada está en la ficha del paciente mediante Abrir expediente clínico. Solo los odontólogos con perfil asociado pueden acceder al contenido clínico.

La anamnesis permite consultar y actualizar alergias, antecedentes y medicamentos. Los tratamientos permiten registrar, consultar y editar diagnóstico, plan, fecha de inicio y estado PLANIFICADO, EN_CURSO o FINALIZADO. Cada tratamiento contiene observaciones de evolución con fecha y odontólogo autor. Solo el autor puede corregir su observación.

Se conserva el odontograma existente del equipo, que permite seleccionar pieza y cara y mantiene el historial de registros; se agregaron enlaces entre este módulo y el expediente. Esta funcionalidad preexistente no se presenta como desarrollo nuevo de P5.

La carga de archivos acepta JPG, PNG y PDF de hasta 5120 KB. Se valida la extensión y MIME en el servidor y el formato y tamaño en el navegador. El contenido se guarda en el disco local privado; la base guarda su referencia. El visor y la descarga comprueban autenticación, rol clínico y pertenencia del archivo al paciente. Si falla la inserción en base, se elimina el archivo recién guardado.

## Datos de archivos
La migración crea archivo_clinico con id, id_expediente, id_odontologo, nombre_original, ruta única, mime, tamano en bytes, descripcion y fecha. Las claves foráneas apuntan a expediente y odontologo. Los archivos permanecen en storage/app/private/clinicos, fuera de public. No es necesario ejecutar storage:link para este módulo.

## Integración con el proyecto del equipo
La entrega se prepara en `feature/p5-expediente-archivos`, basada en `develop` actualizado al commit `6215057`. El autor de los commits es Soophi00. La integración se solicita mediante Pull Request hacia `develop`, para revisión del equipo.

1. Descargar la rama del Pull Request y revisar sus cambios antes de integrar.
2. Resolver cualquier cambio concurrente del equipo mediante el flujo de revisión habitual.
3. Ejecutar composer install, configurar .env y APP_KEY según el README del equipo.
4. Ejecutar php artisan migrate. No usar migrate:fresh en una base con información que deba conservarse.
5. Configurar upload_max_filesize al menos en 5M y post_max_size por encima de 5M. Dar permiso de escritura a storage y bootstrap/cache.
6. Iniciar con php artisan serve e ingresar con una cuenta ODONTOLOGO que tenga perfil asociado.
7. Abrir Pacientes, elegir una ficha y pulsar Abrir expediente clínico.
8. Ejecutar php artisan test --filter=ExpedienteClinicoTest y las pruebas OdontogramaTest en la base de pruebas MySQL del equipo. Revisar la configuración antes de ejecutar: RefreshDatabase reconstruye la base de pruebas.
9. Solicitar a P4 la prueba cruzada de P5 y entregar a P1 los archivos y evidencias para integrar mediante PR.

## Validación realizada
Se ejecutaron ExpedienteClinicoTest y OdontogramaTest: 9 pruebas aprobadas, 83 aserciones. Entorno utilizado: PHP 8.5.11 de Laragon y SQLite en archivo aislado. Se comprobó la compilación de vistas Blade y se aplicó formato Pint a los archivos PHP nuevos o modificados. No se ha certificado ejecución sobre MySQL ni validación visual en dos navegadores o móvil.

Las pruebas comprueban el acceso sin sesión, rechazo del rol Recepcionista, flujo de anamnesis, tratamiento y evolución, lectura del expediente, rechazo de tratamiento ajeno al paciente, carga y consulta privada de archivo, rechazo de archivo ajeno al paciente, archivo de más de 5 MB y formato no permitido. Las pruebas existentes del odontograma verifican sus flujos e historial.

## Matriz de comprobación manual pendiente
| Caso | Acción | Resultado esperado | Estado |
|---|---|---|---|
| P5-01 | Guardar anamnesis y recargar | Persisten los datos | Pendiente en navegador |
| P5-02 | Crear y editar tratamiento | Se muestran datos y estado actualizado | Pendiente en navegador |
| P5-03 | Agregar y corregir evolución propia | Se conserva fecha y autor | Pendiente en navegador |
| P5-04 | Registrar dos condiciones de una pieza y cara | Se conserva historial y se muestra la última condición | Pendiente en navegador |
| P5-05 | Cargar JPG, PNG y PDF válidos | Archivo visible y descargable | Pendiente en navegador |
| P5-06 | Cargar más de 5 MB o formato inválido | Rechazo en cliente y servidor | Pendiente en navegador |
| P5-07 | Abrir URL clínica sin sesión y con recepción | Redirección a login o 403 | Pendiente en navegador |
| P5-08 | Repetir flujos en Chrome, Edge y móvil | Formularios legibles y acciones utilizables | Pendiente |

## Prueba cruzada de P6
La copia revisada contiene DashboardController con conteos de pacientes, citas y tratamientos. No se encontró ReporteController implementado. Por tanto, no se afirma que los reportes de P6 estén probados. Cuando P6 entregue su módulo, verificar filtros por fechas, doctor y estado, correspondencia de cifras con la base, gráficos sin datos y con datos, PDF y Excel, y exclusión de ingresos. Registrar cada diferencia con pasos, resultado esperado, resultado obtenido, prioridad, responsable P6 y estado.

## Registro de pendientes
P5-M01: Validación MySQL y navegadores. Responsable P5 con apoyo de P4. Estado pendiente.
P5-M02: Capturas reales del expediente, odontograma, carga válida, rechazo de archivo y acceso denegado. Responsable P5. Estado pendiente.
P6-M01: Reportes y exportaciones no verificables con el código revisado. Responsable P6. Estado pendiente de entrega.

## Sección para el informe del equipo
El módulo de expediente clínico integra anamnesis, tratamientos, evolución y consulta del odontograma. La información se vincula al paciente y se restringe al perfil odontológico. Se incorporó almacenamiento privado de radiografías y documentos con validación de formato y tamaño, metadatos en la base y consulta protegida. Las pruebas automatizadas ejecutadas en un entorno aislado aprobaron 9 casos con 83 comprobaciones. La verificación en MySQL, navegadores y móvil y las capturas de defensa quedan pendientes y deberán incorporarse como evidencias reales.

## Guion de defensa de P5
Mi responsabilidad es el expediente clínico, el odontograma y los archivos del paciente. Desde la ficha abro el expediente y consulto o actualizo la anamnesis. A continuación registro un tratamiento, indico el diagnóstico, el plan y su estado y agrego una evolución con autor y fecha. Abro el odontograma integrado, selecciono pieza y cara y muestro que el historial conserva los registros. Finalmente cargo una radiografía o PDF y demuestro su consulta protegida. Un archivo que supera el límite o tiene un formato no admitido se rechaza. Los archivos se guardan fuera de la carpeta pública y todas las consultas clínicas exigen el perfil odontológico.

Para la demostración usar exclusivamente pacientes ficticios. Mostrar también un rechazo real de validación y acceso con recepción. No presentar pruebas pendientes como aprobadas.
