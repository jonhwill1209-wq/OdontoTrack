# Matriz de pruebas de integración

## Evidencia automatizada

Ejecución registrada: `php artisan test` — **125 pruebas aprobadas, 941 aserciones** (suite del 7 de noviembre de 2026).
| ID | Módulo | Caso de prueba | Pasos / cobertura | Resultado esperado | Resultado obtenido | Navegador | Estado |
|---|---|---|---|---|---|---|---|
| INT-01 | Autenticación | Inicio de sesión, bloqueo y cierre | `AutenticacionTest` | Aceptar credenciales válidas, limitar intentos y cerrar sesión | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-02 | Roles | Rutas y datos permitidos por rol | `ControlAccesoTest`, `PacienteTest` | Denegar operaciones no autorizadas y limitar agenda al odontólogo | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-03 | Base de datos | Esquema y relaciones principales | `BaseDatosTest` | Crear tablas y hacer cumplir unicidad y claves foráneas | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-04 | Pacientes | CRUD, búsqueda, estado y validaciones | `PacienteTest`, `PacienteValidacionTest` | Persistir datos válidos y conservar los originales ante errores | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-05 | Citas | CRUD, filtros, solapamiento y cancelación | `CitaTest` | Impedir conflictos y conservar citas canceladas | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-06 | Odontograma | Permisos, validación e historial | `OdontogramaTest` | Persistir cada cambio con el profesional autenticado y conservar historial | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-07 | Exportación de pacientes | Excel, filtros y control de rol | `PacienteExportacionTest` | Descargar XLSX con datos y filtros correctos | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-08 | Expediente | Anamnesis, carga, descarga y eliminación de archivo | `ExpedienteTratamientoReporteTest` | Persistir datos y archivos privados; denegar roles no autorizados | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-09 | Tratamientos | Alta, edición, evolución, validación y eliminación protegida | `ExpedienteTratamientoReporteTest` | Registrar historial clínico y no borrar tratamientos con evoluciones | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| INT-10 | Reportes | Período, métricas, Excel, PDF y alcance por rol | `ExpedienteTratamientoReporteTest` | Filtrar por fecha, exportar y limitar al odontólogo autenticado | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobada |
| MAN-01 | Interfaz adaptable | Navegadores de escritorio y móvil | Abrir login, dashboard, agenda, expedientes y reportes en Chrome, Firefox, Edge y Safari; revisar anchos móvil/tableta/escritorio | Vistas utilizables sin desbordamientos ni errores de interacción | No ejecutado en navegador real desde este entorno | Pendiente | Pendiente |
| MAN-02 | Integración visual | Recorrido con roles reales y datos de prueba | Ejecutar manualmente altas, edición, baja, archivos y descargas con odontólogo y recepcionista | Flujo completo desde la interfaz, mensajes claros y permisos correctos | Cubierto parcialmente por pruebas HTTP; falta recorrido visual manual | Pendiente | Pendiente |
| DEF-01 | El módulo Usuarios funciona en `/usuarios`, pero no aparece en el menú de Recepcionista | Navegación y usuarios | Persona 2 | Media | Pendiente |
| DEF-02 | El campo Nombre completo rechaza letras mayúsculas aunque el valor contiene únicamente letras y espacios | Usuarios | Persona 2 | Media | Pendiente |
| CP-07 | Usuarios | Crear cuenta e iniciar sesión | Crear usuario de recepción y acceder con sus credenciales | La cuenta se crea y permite iniciar sesión con rol Recepcionista | Cuenta creada e inicio de sesión correcto | Firefox | Aprobado |
| CP-08 | Reportes | Consultar y exportar reporte de citas | Abrir Reportes y descargar Excel y PDF del período mostrado | La vista carga y ambos archivos se descargan sin errores | Reporte cargado; Excel y PDF descargados y abiertos correctamente | Firefox | Aprobado |
| CP-09 | Agenda de citas | Crear y marcar una cita como realizada | Agendar una cita para Ana Prueba y cambiar su estado a realizada | La cita aparece en agenda y su estado cambia correctamente | Cita creada para el 10/11/2026 y marcada como REALIZADA | Firefox | Aprobado |
| CP-10 | Integración Agenda y Reportes | Reflejar cita realizada en reportes | Crear una cita, marcarla realizada y consultar Reportes del mismo período | Los indicadores y detalle incluyen la cita realizada | Total: 1; realizadas: 1; gráfica y detalle muestran la cita de Ana Prueba | Firefox | Aprobado |
| CP-11 | Agenda de citas | Evitar solapamiento de odontólogo o consultorio | Crear una cita y luego intentar otra con el mismo horario, odontólogo y consultorio | El sistema rechaza la segunda cita e informa el conflicto | Se mostró: “El odontólogo o el consultorio ya tienen una cita en ese horario” | Firefox | Aprobado |
| CP-12 | Interfaz | Visualización móvil | Verificar panel y menú en pantalla de 390 × 844 píxeles | El contenido se adapta sin cortes ni errores | Pendiente: este entorno no permite activar la vista adaptable | — | Pendiente |
| CP-13 | Expedientes | Abrir expediente clínico como odontólogo | Iniciar como odontóloga, abrir paciente y presionar Abrir expediente clínico | El expediente carga sin errores | Fallido: Error 500, no existe la tabla expediente_archivo | Firefox | Fallido |
| CP-14 | Odontograma | Cargar odontograma de paciente | Iniciar como odontóloga, abrir paciente y seleccionar Ver odontograma | Se muestran las piezas dentales y el formulario sin errores | Odontograma de Ana Prueba cargado correctamente | Firefox | Aprobado |
| CP-12 | Interfaz | Visualización móvil | Abrir el panel desde un teléfono conectado a la misma red | El contenido se adapta sin cortes ni errores | Panel abierto correctamente en Chrome Android; menú y tarjetas visibles | Chrome Android | Aprobado |

Las p| CP-12 | Interfaz | Visualización móvil | Verificar panel y menú en pantalla de 390 × 844 píxeles | El contenido se adapta sin cortes ni errores | Pendiente: este entorno no permite activar la vista adaptable | — | Pendiente |uebas automatizadas no sustituyen las verificaciones manuales de navegador indicadas. Actualizar las columnas de resultado, navegador y estado al completar estas últimas.
