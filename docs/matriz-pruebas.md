# Matriz de pruebas

Última suite completa registrada: `php artisan test` - **125 pruebas aprobadas, 941 aserciones**.

Revalidación posterior de Usuarios: `php artisan test --filter=UsuarioTest` - **17 pruebas aprobadas, 114 aserciones**.

| ID | Módulo | Caso de prueba | Pasos / cobertura | Resultado esperado | Resultado obtenido | Navegador | Estado |
|---|---|---|---|---|---|---|---|
| INT-01 | Autenticación | Inicio de sesión, bloqueo y cierre | `AutenticacionTest` | Aceptar credenciales válidas, limitar intentos y cerrar sesión | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-02 | Roles | Rutas y datos permitidos por rol | `ControlAccesoTest`, `PacienteTest` | Denegar operaciones no autorizadas y limitar agenda al odontólogo | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-03 | Base de datos | Esquema y relaciones principales | `BaseDatosTest` | Crear tablas y hacer cumplir unicidad y claves foráneas | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-04 | Pacientes | CRUD, búsqueda, estado y validaciones | `PacienteTest`, `PacienteValidacionTest` | Persistir datos válidos y conservar los originales ante errores | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-05 | Citas | CRUD, filtros, solapamiento y cancelación | `CitaTest` | Impedir conflictos y conservar citas canceladas | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-06 | Odontograma | Permisos, validación e historial | `OdontogramaTest` | Persistir cada cambio con el profesional autenticado y conservar historial | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-07 | Exportación de pacientes | Excel, filtros y control de rol | `PacienteExportacionTest` | Descargar XLSX con datos y filtros correctos | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-08 | Expediente | Anamnesis, carga, descarga y eliminación de archivo | `ExpedienteTratamientoReporteTest` | Persistir datos y archivos privados; denegar roles no autorizados | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-09 | Tratamientos | Alta, edición, evolución, validación y eliminación protegida | `ExpedienteTratamientoReporteTest` | Registrar historial clínico y no borrar tratamientos con evoluciones | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| INT-10 | Reportes | Período, métricas, Excel, PDF y alcance por rol | `ExpedienteTratamientoReporteTest` | Filtrar por fecha, exportar y limitar al odontólogo autenticado | Aprobado en pruebas Laravel | No aplica (HTTP) | Aprobado |
| CP-01 | Usuarios | Crear cuenta e iniciar sesión | Crear usuario de recepción y acceder con sus credenciales | La cuenta se crea y permite iniciar sesión con rol Recepcionista | Cuenta creada e inicio de sesión correcto | Firefox | Aprobado |
| CP-02 | Reportes | Consultar y exportar reporte de citas | Abrir Reportes y descargar Excel y PDF | La vista carga y ambos archivos se descargan sin errores | Reporte cargado; Excel y PDF descargados y abiertos correctamente | Firefox | Aprobado |
| CP-03 | Agenda de citas | Crear y marcar una cita como realizada | Agendar una cita para Ana Prueba y cambiar su estado | La cita aparece en agenda y su estado cambia correctamente | Cita creada y marcada como REALIZADA | Firefox | Aprobado |
| CP-04 | Integración Agenda y Reportes | Reflejar cita realizada en reportes | Crear cita, marcarla realizada y consultar Reportes | Los indicadores y detalle incluyen la cita realizada | Total 1; realizadas 1; gráfica y detalle correctos | Firefox | Aprobado |
| CP-05 | Agenda de citas | Evitar solapamiento | Intentar crear una segunda cita con el mismo horario, odontóloga y consultorio | El sistema rechaza la segunda cita e informa el conflicto | Se mostró el mensaje de conflicto de horario | Firefox | Aprobado |
| CP-06 | Expedientes | Abrir expediente clínico como odontóloga | Abrir paciente y presionar Abrir expediente clínico | El expediente carga sin errores | Fallido: Error 500; falta la tabla `expediente_archivo` | Firefox | Fallido |
| CP-07 | Odontograma | Cargar odontograma de paciente | Abrir paciente y seleccionar Ver odontograma | Se muestran piezas dentales y formulario sin errores | Odontograma de Ana Prueba cargado correctamente | Firefox | Aprobado |
| CP-08 | Interfaz | Visualización móvil | Abrir el panel desde un teléfono conectado a la misma red | El contenido se adapta sin cortes ni errores | Panel abierto; menú y tarjetas visibles | Chrome Android | Aprobado |
| CP-09 | Usuarios | Revalidar menú y nombres con mayúsculas | Iniciar como Recepcionista, abrir Usuarios desde Administración y crear Recepcion QA Dos | El menú se muestra y el usuario con mayúsculas se crea sin errores | Menú Administración visible; usuario creado correctamente | Firefox | Aprobado |
| CP-10 | Compatibilidad | Recorrido en navegador adicional | Revisar login, dashboard, agenda, expedientes y reportes en otro navegador de escritorio | Vistas utilizables sin errores de interacción | Pendiente de ejecución | Edge o Chrome | Pendiente |
| CP-10 | Compatibilidad | Recorrido en Firefox | Abrir inicio de sesión, dashboard, agenda y reportes en Firefox | Las vistas cargan sin errores de interacción | Todas las pantallas verificadas cargaron correctamente | Firefox | Aprobado |
## Pendientes de cierre

- Corregir y revalidar `DEF-10`: error al abrir expediente clínico por tabla `expediente_archivo` faltante.
- Ejecutar la prueba de compatibilidad `CP-10`.
- Ejecutar nuevamente la suite completa después de integrar la corrección pendiente.
