# Registro de defectos y pendientes

| ID | Descripción | Módulo | Responsable | Prioridad | Estado |
|---|---|---|---|---|---|
| DEF-01 | Expedientes no tenía controlador ni vistas operativas para anamnesis y consulta clínica. | Expedientes | Equipo de desarrollo | Alta | Corregido; cubierto por INT-08 |
| DEF-02 | Tratamientos no tenía operaciones implementadas ni registro de evolución clínica. | Tratamientos | Equipo de desarrollo | Alta | Corregido; cubierto por INT-09 |
| DEF-03 | El expediente no permitía adjuntar, consultar ni retirar documentos. | Archivos clínicos | Equipo de desarrollo | Alta | Corregido; adjuntos privados con validación y pruebas |
| DEF-04 | No había reportes de citas con métricas ni descarga PDF/Excel. | Reportes | Equipo de desarrollo | Media | Corregido; cubierto por INT-10 |
| DEF-05 | La matriz de pruebas y el registro de defectos estaban vacíos. | QA y documentación | Equipo de desarrollo | Media | Corregido |
| DEF-06 | No había evidencia de pruebas móviles. | QA responsive | QA | Media | Corregido; validado en Chrome Android con CP-12 |
| DEF-08 | El enlace Usuarios no aparecía en el menú de Recepcionista. | Usuarios | Persona 2 | Media | Corregido; validado manualmente |
| DEF-09 | El campo Nombre completo rechazaba letras mayúsculas válidas. | Usuarios | Persona 2 | Media | Corregido; validado manualmente |
| DEF-07 | La administración de usuarios no estaba definida en el alcance. | Usuarios | Equipo | Baja | Corregido; Recepcionista administra usuarios, odontólogos y consultorios |

| DEF-10 | Al abrir un expediente clínico ocurre Error 500 porque no existe la tabla expediente_archivo. | Expedientes y archivos | Persona 5 | Alta | Pendiente |

## Criterios de cierre

- Los defectos corregidos deben validarse manualmente y anotarse en `docs/matriz-pruebas.md`.
- DEF-08 y DEF-09 requieren una nueva prueba luego de integrar la corrección de Persona 2.
- DEF-10 bloquea el cierre de la Release Candidate hasta que Persona 5 lo corrija y se valide.
