# Registro de defectos y pendientes

| ID | Descripción | Módulo | Responsable | Prioridad | Estado |
|---|---|---|---|---|---|
| DEF-01 | Expedientes no tenía controlador ni vistas operativas para anamnesis y consulta clínica. | Expedientes | Equipo de desarrollo | Alta | Corregido; cubierto por INT-08 |
| DEF-02 | Tratamientos no tenía operaciones implementadas ni registro de evolución clínica. | Tratamientos | Equipo de desarrollo | Alta | Corregido; cubierto por INT-09 |
| DEF-03 | El expediente no permitía adjuntar, consultar ni retirar documentos. | Archivos clínicos | Equipo de desarrollo | Alta | Corregido; adjuntos privados con validación y pruebas |
| DEF-04 | No había reportes de citas con métricas ni descarga PDF/Excel. | Reportes | Equipo de desarrollo | Media | Corregido; cubierto por INT-10 |
| DEF-05 | La matriz de pruebas y el registro de defectos estaban vacíos. | QA y documentación | Equipo de desarrollo | Media | Corregido |
| DEF-06 | No había evidencia de pruebas móviles. | QA responsive | QA | Media | Corregido; validado en Chrome Android con CP-12 |
| DEF-07 | La administración de usuarios no estaba definida en el alcance. | Usuarios | Equipo | Baja | Corregido; Recepcionista administra usuarios, odontólogos y consultorios |
| DEF-08 | El enlace Usuarios no aparecía en el menú de Recepcionista. | Usuarios | Persona 2 | Media | Corregido; validado manualmente |
| DEF-09 | El campo Nombre completo rechazaba letras mayúsculas válidas. | Usuarios | Persona 2 | Media | Corregido; validado manualmente |
| DEF-10 | Al abrir un expediente clínico ocurría Error 500 por migraciones locales pendientes. | Expedientes y archivos | QA | Alta | Corregido; se ejecutaron las migraciones pendientes |

## Criterios de cierre

- Los defectos corregidos deben validarse manualmente y anotarse en `docs/matriz-pruebas.md`.
- DEF-08 y DEF-09 requieren una nueva prueba luego de integrar la corrección de Persona 2.
- DEF-10 corresponde al Error 500 por `expediente_archivo` faltante, reportado inicialmente como DEF-04 por P5; DEF-04 identifica Reportes en este registro consolidado. QA ya documentó su recuperación con migraciones y aprobó CP-06. Cada instalación debe aplicar y verificar las migraciones antes de la RC; el cierre local no garantiza que otra base esté actualizada.


## Revisión P5 del error de tabla faltante

El 8 de noviembre de 2026 se verificó la correspondencia entre `Expediente::archivos()`, `ExpedienteArchivo` y la migración `2026_11_07_000001_create_expediente_archivo_table`. El esquema requerido está incluido en develop. La causa documentada por QA fue una migración pendiente en su instalación; no se requiere otra tabla o una segunda migración duplicada.

Se añadió `ExpedienteMigracionTest`, que simula una instalación previa a la tabla, ejecuta la actualización y verifica apertura del expediente, conservación de paciente y anamnesis y repetición segura del comando. Con los casos de expediente, odontograma, archivos y reportes se aprobaron 16 pruebas y 148 aserciones en MySQL 8.4.3 aislado. El README incorpora el procedimiento de actualización y diagnóstico. La corrección operativa debe aplicarse a cada instalación; estas pruebas no ejecutan migraciones sobre la base local del reporte.


### Evidencia ampliada de P5

Se ejecutó la batería completa local: **128 pruebas aprobadas y 972 aserciones**, con MySQL 8.4.3 aislado y PHP 8.5.11, salida 0. Se adjuntan [dos capturas reales en VS Code y el registro completo](evidencias/p5/README.md). Este resultado amplía la validación de 16 casos del módulo; no implica que se hayan ejecutado migraciones en la instalación que reportó el error.
