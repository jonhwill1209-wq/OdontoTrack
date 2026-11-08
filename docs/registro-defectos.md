# Registro de defectos y pendientes

| ID | Descripción | Módulo | Responsable | Prioridad | Estado |
|---|---|---|---|---|---|
| DEF-01 | Expedientes no tenía controlador ni vistas operativas para anamnesis y consulta clínica. | Expedientes | Equipo de desarrollo | Alta | Corregido; cubierto por INT-08 |
| DEF-02 | Tratamientos no tenía operaciones implementadas ni registro de evolución clínica. | Tratamientos | Equipo de desarrollo | Alta | Corregido; cubierto por INT-09 |
| DEF-03 | El expediente no permitía adjuntar, consultar ni retirar documentos. | Archivos clínicos | Equipo de desarrollo | Alta | Corregido; adjuntos privados con validación y pruebas |
| DEF-04 | No había reportes de citas con métricas ni descarga PDF/Excel. | Reportes | Equipo de desarrollo | Media | Corregido; cubierto por INT-10 |
| DEF-05 | La matriz de pruebas y el registro de defectos estaban vacíos. | QA / documentación | Equipo de desarrollo | Media | Corregido; contiene evidencia automatizada y pendientes manuales |
| DEF-06 | No hay evidencia de comprobación en navegadores distintos ni en varias resoluciones. | QA / responsive | QA / equipo de desarrollo | Media | Pendiente; requiere ejecución manual en navegador |
| DEF-07 | La plantilla de rutas/vistas de usuarios está vacía y no existe un rol administrador definido en el alcance actual. | Administración de usuarios | Product owner / equipo | Baja | Pendiente de decisión de alcance; no se habilitó gestión de cuentas que permita elevar privilegios |
| DEF-03 | El campo Nombre completo rechaza letras mayúsculas aunque el valor contiene únicamente letras y espacios | Usuarios | Persona 2 | Media | Pendiente |
| DEF-04 | Al abrir un expediente clínico ocurre Error 500 porque no existe la tabla expediente_archivo | Expedientes y archivos | Persona 5 | Alta | Pendiente |


## Criterios de cierre

- Los defectos corregidos se respaldan con las pruebas listadas en `docs/matriz-pruebas.md`.
- DEF-06 requiere registrar navegador, resolución y resultado de la comprobación manual.
- DEF-07 requiere confirmar si la administración de usuarios forma parte del alcance y qué rol puede realizarla antes de exponer operaciones para modificar cuentas.
|----|-------------|--------|-------------|-----------|--------|
sin defectos detectados en las pruebas iniciales de integracion realizadas el 5 de noviembre de 2023


## Seguimiento de validación P5

Los siguientes puntos son pendientes de validación, no fallos funcionales confirmados.

| ID | Descripción | Módulo | Responsable | Prioridad | Estado |
|---|---|---|---|---|---|
| P5-VAL-01 | Repetir pruebas sobre MySQL del equipo | Expediente y archivos | P5 | Alta | Pendiente |
| P5-VAL-02 | Prueba manual en dos navegadores y móvil y capturas de evidencia | Expediente y archivos | P5 con prueba cruzada P4 | Alta | Pendiente |
| P5-VAL-03 | Probar reportes, gráficos y exportaciones cuando P6 entregue el módulo | Dashboard y reportes | P5 y P6 | Media | Pendiente de entrega P6 |
