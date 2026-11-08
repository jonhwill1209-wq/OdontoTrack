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

Las pruebas automatizadas no sustituyen las verificaciones manuales de navegador indicadas. Actualizar las columnas de resultado, navegador y estado al completar estas últimas.
