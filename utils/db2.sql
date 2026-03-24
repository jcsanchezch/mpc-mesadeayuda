-- ============================================================
--  BASE DE DATOS: Sistema de Tickets TI
--  Municipalidad Provincial
--  Oficina de Tecnologías de la Información (OTI)
--  Marco: ITIL 4
--  Motor: PostgreSQL
--  Autenticación: Laravel Sanctum (personal_access_tokens)
--  Roles y permisos: Spatie Laravel-Permission
-- ============================================================
--  MÓDULOS:
--    1.  Trabajadores (referencia al User de Laravel)
--    2.  Catálogo de servicios
--    3.  SLA, horario laboral y feriados
--    4.  Inventario básico de activos TI
--    5.  Tickets / solicitudes de servicio
--          5.1  Ticket (tabla principal)
--          5.2  Aprobaciones
--          5.3  Movimientos
--          5.4  Solicitudes de información
--          5.5  Transferencias
--          5.6  Adjuntos
--          5.7  Pausas de SLA
--          5.8  Datos de cierre
--          5.9  Conformidad del usuario
--    6.  Incidentes
--          6.1  Incidente (tabla principal)
--          6.2  Aprobaciones de incidente
--          6.3  Movimientos de incidente
--          6.4  Solicitudes de información
--          6.5  Transferencias
--          6.6  Adjuntos
--          6.7  Pausas de SLA
--          6.8  Datos de cierre
--          6.9  Conformidad del usuario
--    7.  Base de conocimiento y autoservicio
--    8.  Plantillas de respuesta
--    9.  Notificaciones
--    10. Reportes de capacitación
--    11. Auditoría y seguridad
--    12. Vistas y vistas materializadas
--    13. Triggers de auditoría
--    14. Índices
--    15. Datos semilla
-- ============================================================
--
--  NOTAS DE INTEGRACIÓN CON LARAVEL:
--  · La tabla `users` la gestiona Laravel (email, password, remember_token).
--  · Los roles ('solicitante', 'tecnico', 'supervisor', 'administrador')
--    los gestiona Spatie Laravel-Permission (tabla `roles` / `model_has_roles`).
--  · Los tokens de sesión los gestiona Sanctum (tabla `personal_access_tokens`).
--  · `trabajadores` extiende `users` con datos institucionales (DNI, área, cargo).
-- ============================================================


-- ============================================================
-- 1. TRABAJADORES
-- ============================================================

CREATE TABLE areas (
    id_area         BIGSERIAL    PRIMARY KEY,
    nombre          VARCHAR(150) NOT NULL,
    descripcion     TEXT,
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP    NOT NULL DEFAULT NOW()
);

CREATE TABLE cargos (
    id_cargo        BIGSERIAL    PRIMARY KEY,
    nombre          VARCHAR(150) NOT NULL,
    descripcion     TEXT,
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP    NOT NULL DEFAULT NOW()
);

-- Extiende la tabla `users` de Laravel con datos institucionales.
-- · La autenticación (email, password, remember_token) vive en `users`.
-- · Los roles se asignan vía Spatie en `model_has_roles`.
-- · Los tokens de API se gestionan con Sanctum en `personal_access_tokens`.
CREATE TABLE trabajadores (
    id_trabajador   BIGSERIAL    PRIMARY KEY,
    -- FK al User de Laravel
    user_id         BIGINT       NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    dni             CHAR(8)      NOT NULL UNIQUE,
    nombres         VARCHAR(100) NOT NULL,
    apellidos       VARCHAR(100) NOT NULL,
    telefono        VARCHAR(20),
    id_area         BIGINT       REFERENCES areas(id_area),
    id_cargo        BIGINT       REFERENCES cargos(id_cargo),
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN trabajadores.user_id IS
  'FK al registro User de Laravel. Email y contraseña se gestionan en users.';
COMMENT ON TABLE trabajadores IS
  'Datos institucionales del trabajador municipal. Los roles (solicitante, tecnico,
   supervisor, administrador) se asignan mediante Spatie Laravel-Permission.';


-- ============================================================
-- 2. CATÁLOGO DE SERVICIOS
-- ============================================================

CREATE TABLE categoria_servicios (
    id_categoria    BIGSERIAL    PRIMARY KEY,
    nombre          VARCHAR(150) NOT NULL,
    descripcion     TEXT,
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP    NOT NULL DEFAULT NOW()
);

CREATE TABLE slas (
    id_sla                      BIGSERIAL    PRIMARY KEY,
    nombre                      VARCHAR(150) NOT NULL,
    descripcion                 TEXT,
    -- 1=Crítico | 2=Alto | 3=Medio | 4=Bajo
    prioridad                   SMALLINT     NOT NULL CHECK (prioridad BETWEEN 1 AND 4),
    tiempo_respuesta_h          NUMERIC(5,2) NOT NULL,
    tiempo_resolucion_h         NUMERIC(5,2) NOT NULL,
    -- Días laborables para conformidad automática tras notificar resolución
    dias_conformidad_automatica SMALLINT     NOT NULL DEFAULT 3,
    activo                      BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at                  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN slas.prioridad                   IS '1=Crítico, 2=Alto, 3=Medio, 4=Bajo';
COMMENT ON COLUMN slas.tiempo_respuesta_h          IS 'Horas laborables para el primer contacto con el solicitante';
COMMENT ON COLUMN slas.tiempo_resolucion_h         IS 'Horas laborables máximas para resolver y cerrar el ticket';
COMMENT ON COLUMN slas.dias_conformidad_automatica IS
  'Días laborables tras notificar la resolución antes de registrar conformidad automática';

CREATE TABLE servicios (
    id_servicio          BIGSERIAL    PRIMARY KEY,
    id_categoria         BIGINT       NOT NULL REFERENCES categoria_servicios(id_categoria),
    id_sla_defecto       BIGINT       REFERENCES slas(id_sla),
    nombre               VARCHAR(200) NOT NULL,
    descripcion          TEXT,
    -- 'solicitud' | 'incidente'
    tipo                 VARCHAR(30)  NOT NULL DEFAULT 'solicitud',
    requiere_aprobacion  BOOLEAN      NOT NULL DEFAULT FALSE,
    -- Quién debe aprobar: 'jefe_area' | 'supervisor_oti' | 'ambos'
    tipo_aprobador       VARCHAR(30),
    permite_autoservicio BOOLEAN      NOT NULL DEFAULT FALSE,
    activo               BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at           TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at           TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN servicios.tipo IS
  'solicitud: petición planificable del catálogo;
   incidente: interrupción no planificada de un servicio';
COMMENT ON COLUMN servicios.tipo_aprobador IS
  'jefe_area: aprueba el jefe del área solicitante;
   supervisor_oti: aprueba el supervisor de la OTI;
   ambos: requiere aprobación de ambos en secuencia';

-- Técnicos habilitados para atender cada servicio
CREATE TABLE servicio_tecnico (
    id_servicio     BIGINT NOT NULL REFERENCES servicios(id_servicio),
    id_trabajador   BIGINT NOT NULL REFERENCES trabajadores(id_trabajador),
    PRIMARY KEY (id_servicio, id_trabajador)
);


-- ============================================================
-- 3. SLA, HORARIO LABORAL Y FERIADOS
-- ============================================================

-- Horario laboral de la municipalidad
CREATE TABLE horarios_laborales (
    id_horario      BIGSERIAL   PRIMARY KEY,
    -- 1=Lunes ... 7=Domingo (ISO)
    dia_semana      SMALLINT    NOT NULL CHECK (dia_semana BETWEEN 1 AND 7),
    hora_inicio     TIME        NOT NULL,
    hora_fin        TIME        NOT NULL,
    activo          BOOLEAN     NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP   NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP   NOT NULL DEFAULT NOW(),
    CONSTRAINT horario_horas_validas CHECK (hora_fin > hora_inicio)
);

COMMENT ON COLUMN horarios_laborales.dia_semana IS
  '1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado, 7=Domingo';

-- Feriados nacionales y locales
CREATE TABLE feriados (
    id_feriado      BIGSERIAL    PRIMARY KEY,
    fecha           DATE         NOT NULL UNIQUE,
    nombre          VARCHAR(150) NOT NULL,
    -- 'nacional' | 'regional' | 'local'
    tipo            VARCHAR(20)  NOT NULL DEFAULT 'nacional',
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN feriados.tipo IS
  'nacional: feriado del calendario peruano;
   regional: feriado de la región;
   local: feriado propio del municipio';


-- ============================================================
-- 4. INVENTARIO BÁSICO DE ACTIVOS TI
-- ============================================================

CREATE TABLE activos_ti (
    id_activo              BIGSERIAL    PRIMARY KEY,
    codigo_activo          VARCHAR(50)  NOT NULL UNIQUE,
    -- 'computadora' | 'laptop' | 'impresora' | 'scanner' | 'switch' |
    -- 'router' | 'servidor' | 'ups' | 'telefono_ip' | 'otro'
    tipo                   VARCHAR(50)  NOT NULL,
    marca                  VARCHAR(100),
    modelo                 VARCHAR(150),
    numero_serie           VARCHAR(100) UNIQUE,
    id_area                BIGINT       REFERENCES areas(id_area),
    id_trabajador_asignado BIGINT       REFERENCES trabajadores(id_trabajador),
    -- 'operativo' | 'en_mantenimiento' | 'de_baja' | 'en_almacen'
    estado                 VARCHAR(30)  NOT NULL DEFAULT 'operativo',
    fecha_adquisicion      DATE,
    garantia_hasta         DATE,
    observaciones          TEXT,
    activo                 BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at             TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at             TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN activos_ti.tipo IS
  'computadora | laptop | impresora | scanner | switch | router |
   servidor | ups | telefono_ip | otro';
COMMENT ON COLUMN activos_ti.estado IS
  'operativo | en_mantenimiento | de_baja | en_almacen';


-- ============================================================
-- 5. TICKETS / SOLICITUDES DE SERVICIO
-- ============================================================

-- ------------------------------------------------------------
-- 5.1  PLANTILLAS DE RESPUESTA (antes módulo 8 — se adelanta
--       para que ticket_movimientos pueda referenciarlas)
-- ------------------------------------------------------------
CREATE TABLE plantilla_respuestas (
    id_plantilla    BIGSERIAL    PRIMARY KEY,
    id_servicio     BIGINT       REFERENCES servicios(id_servicio),
    id_autor        BIGINT       REFERENCES trabajadores(id_trabajador),
    nombre          VARCHAR(200) NOT NULL,
    -- 'solicitud_info' | 'avance' | 'resolucion' | 'cualquiera'
    tipo_movimiento VARCHAR(30)  NOT NULL DEFAULT 'cualquiera',
    contenido       TEXT         NOT NULL,
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN plantilla_respuestas.contenido IS
  'Texto de la plantilla. Variables disponibles:
   {{nombre_usuario}}, {{codigo_ticket}}, {{nombre_servicio}},
   {{fecha_limite}}, {{nombre_tecnico}}';
COMMENT ON COLUMN plantilla_respuestas.tipo_movimiento IS
  'solicitud_info | avance | resolucion | cualquiera';


-- ------------------------------------------------------------
-- 5.2  TICKET — tabla principal
-- ------------------------------------------------------------
CREATE TABLE tickets (
    id_ticket                BIGSERIAL    PRIMARY KEY,
    -- Código legible: TKT-2025-00123
    codigo                   VARCHAR(20)  NOT NULL UNIQUE,

    id_servicio              BIGINT       NOT NULL REFERENCES servicios(id_servicio),
    id_solicitante           BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),
    id_agente_mesa           BIGINT       REFERENCES trabajadores(id_trabajador),
    id_tecnico_asignado      BIGINT       REFERENCES trabajadores(id_trabajador),
    id_sla                   BIGINT       REFERENCES slas(id_sla),
    id_activo                BIGINT       REFERENCES activos_ti(id_activo),

    -- nuevo | en_clasificacion | pendiente_aprobacion | en_atencion |
    -- pendiente_info | transferido | resuelto | cerrado | cancelado | rechazado
    estado                   VARCHAR(30)  NOT NULL DEFAULT 'nuevo',

    titulo                   VARCHAR(300) NOT NULL,
    descripcion              TEXT         NOT NULL,
    -- 'portal' | 'correo' | 'presencial' | 'telefono'
    canal_ingreso            VARCHAR(30)  NOT NULL DEFAULT 'portal',

    -- Clasificación y priorización (mesa de servicios)
    -- 'solicitud' | 'incidente'
    clasificacion            VARCHAR(30),
    -- 1=Crítico | 2=Alto | 3=Medio | 4=Bajo
    prioridad                SMALLINT     CHECK (prioridad BETWEEN 1 AND 4),
    -- 'alta' | 'media' | 'baja'
    urgencia                 VARCHAR(20),
    -- 'individual' | 'area' | 'institucional'
    impacto                  VARCHAR(20),

    -- Fechas clave del flujo
    fecha_limite_respuesta   TIMESTAMP,
    fecha_limite_resolucion  TIMESTAMP,
    fecha_primera_respuesta  TIMESTAMP,
    fecha_inicio_atencion    TIMESTAMP,
    fecha_resolucion         TIMESTAMP,
    fecha_limite_conformidad TIMESTAMP,
    fecha_conformidad        TIMESTAMP,
    conformidad_automatica   BOOLEAN      NOT NULL DEFAULT FALSE,
    fecha_cierre             TIMESTAMP,

    -- Control de reaperturas
    cantidad_reaperturas     SMALLINT     NOT NULL DEFAULT 0,
    reabierto                BOOLEAN      NOT NULL DEFAULT FALSE,

    -- Satisfacción del usuario
    calificacion             SMALLINT     CHECK (calificacion BETWEEN 1 AND 5),
    comentario_usuario       TEXT,

    created_at               TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at               TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN tickets.codigo IS 'Código legible. Formato sugerido: TKT-YYYY-NNNNN';
COMMENT ON COLUMN tickets.estado IS
  'nuevo > en_clasificacion > pendiente_aprobacion? > en_atencion |
   pendiente_info | transferido > resuelto > cerrado';
COMMENT ON COLUMN tickets.clasificacion IS
  'Determinada por mesa de servicios. Si es incidente se crea registro en tabla incidentes.';
COMMENT ON COLUMN tickets.cantidad_reaperturas IS
  'Contador de veces que el ticket fue reactivado por inconformidad del usuario';
COMMENT ON COLUMN tickets.reabierto IS
  'TRUE si el ticket está actualmente en estado de reapertura';


-- ------------------------------------------------------------
-- 5.3  APROBACIONES DE TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_aprobaciones (
    id_aprobacion    BIGSERIAL   PRIMARY KEY,
    id_ticket        BIGINT      NOT NULL REFERENCES tickets(id_ticket),
    id_aprobador     BIGINT      NOT NULL REFERENCES trabajadores(id_trabajador),
    -- 'jefe_area' | 'supervisor_oti'
    rol_aprobador    VARCHAR(30) NOT NULL,
    -- Orden cuando se requieren varios aprobadores (1, 2, ...)
    orden            SMALLINT    NOT NULL DEFAULT 1,
    -- 'pendiente' | 'aprobado' | 'rechazado'
    decision         VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    comentario       TEXT,
    fecha_decision   TIMESTAMP,
    fecha_limite     TIMESTAMP,
    created_at       TIMESTAMP   NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_aprobaciones.orden IS
  'Orden de aprobación cuando se requieren varios aprobadores en secuencia';
COMMENT ON COLUMN ticket_aprobaciones.fecha_limite IS
  'Si vence sin decisión el sistema puede escalar o notificar al supervisor';


-- ------------------------------------------------------------
-- 5.4  MOVIMIENTOS DE TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_movimientos (
    id_movimiento        BIGSERIAL    PRIMARY KEY,
    id_ticket            BIGINT       NOT NULL REFERENCES tickets(id_ticket),
    id_trabajador        BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),

    -- registro | clasificacion | aprobacion | rechazo |
    -- asignacion | inicio_atencion | avance |
    -- solicitud_info | respuesta_info | transferencia |
    -- resolucion | conformidad | conformidad_auto |
    -- reapertura | cancelacion | nota_interna
    tipo_movimiento      VARCHAR(30)  NOT NULL,

    estado_anterior      VARCHAR(30),
    estado_nuevo         VARCHAR(30),
    id_tecnico_destino   BIGINT       REFERENCES trabajadores(id_trabajador),
    descripcion          TEXT,
    -- Plantilla usada (si aplica)
    id_plantilla         BIGINT       REFERENCES plantilla_respuestas(id_plantilla),
    -- FALSE = nota interna
    visible_solicitante  BOOLEAN      NOT NULL DEFAULT TRUE,

    created_at           TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_movimientos.tipo_movimiento IS
  'registro | clasificacion | aprobacion | rechazo | asignacion |
   inicio_atencion | avance | solicitud_info | respuesta_info |
   transferencia | resolucion | conformidad | conformidad_auto |
   reapertura | cancelacion | nota_interna';


-- ------------------------------------------------------------
-- 5.5  SOLICITUDES DE INFORMACIÓN DE TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_solicitud_infos (
    id_solicitud_info    BIGSERIAL   PRIMARY KEY,
    id_ticket            BIGINT      NOT NULL REFERENCES tickets(id_ticket),
    id_movimiento        BIGINT      NOT NULL REFERENCES ticket_movimientos(id_movimiento),
    id_tecnico           BIGINT      NOT NULL REFERENCES trabajadores(id_trabajador),
    pregunta             TEXT        NOT NULL,
    -- 'pendiente' | 'respondida' | 'vencida'
    estado               VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    respuesta            TEXT,
    id_trabajador_responde BIGINT    REFERENCES trabajadores(id_trabajador),
    fecha_respuesta      TIMESTAMP,
    fecha_limite         TIMESTAMP,
    created_at           TIMESTAMP   NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_solicitud_infos.estado IS
  'pendiente: esperando respuesta; respondida: usuario contestó; vencida: no respondió en plazo';


-- ------------------------------------------------------------
-- 5.6  TRANSFERENCIAS DE TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_transferencias (
    id_transferencia   BIGSERIAL PRIMARY KEY,
    id_ticket          BIGINT    NOT NULL REFERENCES tickets(id_ticket),
    id_movimiento      BIGINT    NOT NULL REFERENCES ticket_movimientos(id_movimiento),
    id_tecnico_origen  BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),
    id_tecnico_destino BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),
    motivo             TEXT      NOT NULL,
    created_at         TIMESTAMP NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 5.7  ADJUNTOS DE TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_adjuntos (
    id_adjunto         BIGSERIAL    PRIMARY KEY,
    id_ticket          BIGINT       NOT NULL REFERENCES tickets(id_ticket),
    id_movimiento      BIGINT       REFERENCES ticket_movimientos(id_movimiento),
    id_solicitud_info  BIGINT       REFERENCES ticket_solicitud_infos(id_solicitud_info),
    id_trabajador      BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),
    nombre_archivo     VARCHAR(255) NOT NULL,
    ruta_almacen       VARCHAR(500) NOT NULL,
    tamano_bytes       BIGINT,
    tipo_mime          VARCHAR(100),
    -- 'todos' | 'solo_oti'
    visibilidad        VARCHAR(20)  NOT NULL DEFAULT 'todos',
    created_at         TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_adjuntos.visibilidad IS
  'todos: visible para solicitante y OTI; solo_oti: archivo interno del equipo técnico';


-- ------------------------------------------------------------
-- 5.8  PAUSAS DE SLA — TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_pausa_slas (
    id_pausa             BIGSERIAL   PRIMARY KEY,
    id_ticket            BIGINT      NOT NULL REFERENCES tickets(id_ticket),
    id_movimiento_inicio BIGINT      NOT NULL REFERENCES ticket_movimientos(id_movimiento),
    id_movimiento_fin    BIGINT      REFERENCES ticket_movimientos(id_movimiento),
    -- 'pendiente_info' | 'fuera_horario' | 'feriado'
    motivo_pausa         VARCHAR(30) NOT NULL,
    fecha_inicio_pausa   TIMESTAMP   NOT NULL,
    fecha_fin_pausa      TIMESTAMP,
    minutos_pausados     INT,
    created_at           TIMESTAMP   NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_pausa_slas.motivo_pausa IS
  'pendiente_info: técnico esperando datos del usuario;
   fuera_horario: ticket abierto fuera del horario laboral;
   feriado: ticket abierto en día no laborable';


-- ------------------------------------------------------------
-- 5.9  DATOS DE CIERRE DE TICKET
--      FK a conocimientos se agrega después de crear esa tabla.
-- ------------------------------------------------------------
CREATE TABLE ticket_cierres (
    id_cierre                    BIGSERIAL PRIMARY KEY,
    id_ticket                    BIGINT    NOT NULL UNIQUE REFERENCES tickets(id_ticket),
    id_tecnico                   BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),

    -- 'conocimiento_ti' | 'hardware' | 'software' | 'configuracion'
    -- | 'red' | 'acceso' | 'proceso' | 'otro'
    categoria_causa              VARCHAR(50)  NOT NULL,
    subcategoria_causa           VARCHAR(100),
    descripcion_causa            TEXT         NOT NULL,
    solucion_aplicada            TEXT         NOT NULL,
    tiempo_resolucion_minutos    INT,
    minutos_pausados_total       INT          NOT NULL DEFAULT 0,
    -- FK a conocimientos — se agrega con ALTER TABLE tras crear esa tabla
    id_conocimiento_usado        BIGINT,

    -- 1=Muy bajo | 2=Bajo | 3=Medio | 4=Alto | 5=Experto
    nivel_conocimiento_ti        SMALLINT     CHECK (nivel_conocimiento_ti BETWEEN 1 AND 5),
    prevenible_con_capacitacion  BOOLEAN      NOT NULL DEFAULT FALSE,
    tema_capacitacion_sugerido   VARCHAR(200),
    genera_articulo_conocimiento BOOLEAN      NOT NULL DEFAULT FALSE,
    observaciones                TEXT,
    created_at                   TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_cierres.categoria_causa IS
  'conocimiento_ti | hardware | software | configuracion | red | acceso | proceso | otro';
COMMENT ON COLUMN ticket_cierres.nivel_conocimiento_ti IS
  '1=Muy bajo | 2=Bajo | 3=Medio | 4=Alto | 5=Experto';
COMMENT ON COLUMN ticket_cierres.minutos_pausados_total IS
  'Suma de todos los minutos en pausa de SLA.';


-- ------------------------------------------------------------
-- 5.10 CONFORMIDAD DEL USUARIO — TICKET
-- ------------------------------------------------------------
CREATE TABLE ticket_conformidades (
    id_conformidad        BIGSERIAL   PRIMARY KEY,
    id_ticket             BIGINT      NOT NULL UNIQUE REFERENCES tickets(id_ticket),
    id_movimiento         BIGINT      NOT NULL REFERENCES ticket_movimientos(id_movimiento),
    -- 'manual' | 'automatica'
    tipo                  VARCHAR(20) NOT NULL DEFAULT 'manual',
    -- 'conforme' | 'no_conforme'
    resultado             VARCHAR(20) NOT NULL,
    motivo_disconformidad TEXT,
    calificacion          SMALLINT    CHECK (calificacion BETWEEN 1 AND 5),
    comentario            TEXT,
    id_trabajador         BIGINT      REFERENCES trabajadores(id_trabajador),
    fecha_conformidad     TIMESTAMP   NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN ticket_conformidades.tipo IS
  'manual: usuario confirmó en el sistema;
   automatica: sistema registró por vencimiento de plazo SLA';
COMMENT ON COLUMN ticket_conformidades.resultado IS
  'conforme: cierra el ticket; no_conforme: reactiva para nueva atención';


-- ============================================================
-- 6. INCIDENTES
-- ============================================================

-- ------------------------------------------------------------
-- 6.1  INCIDENTE — tabla principal
-- ------------------------------------------------------------
CREATE TABLE incidentes (
    id_incidente          BIGSERIAL    PRIMARY KEY,
    -- Código legible: INC-2025-00045
    codigo                VARCHAR(20)  NOT NULL UNIQUE,
    -- Ticket desde el que se originó (si fue reclasificado)
    id_ticket_origen      BIGINT       REFERENCES tickets(id_ticket),

    id_servicio_afectado  BIGINT       NOT NULL REFERENCES servicios(id_servicio),
    id_reportado_por      BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),
    id_agente_mesa        BIGINT       REFERENCES trabajadores(id_trabajador),
    id_tecnico_asignado   BIGINT       REFERENCES trabajadores(id_trabajador),
    id_sla                BIGINT       REFERENCES slas(id_sla),
    id_activo             BIGINT       REFERENCES activos_ti(id_activo),

    -- nuevo | en_clasificacion | en_diagnostico | en_resolucion |
    -- pendiente_info | transferido | resuelto | cerrado | cancelado
    estado                VARCHAR(30)  NOT NULL DEFAULT 'nuevo',

    titulo                VARCHAR(300) NOT NULL,
    descripcion           TEXT         NOT NULL,
    -- 'portal' | 'correo' | 'presencial' | 'telefono'
    canal_ingreso         VARCHAR(30)  NOT NULL DEFAULT 'portal',

    -- 1=Crítico | 2=Alto | 3=Medio | 4=Bajo
    prioridad             SMALLINT     CHECK (prioridad BETWEEN 1 AND 4),
    urgencia              VARCHAR(20),
    -- 'individual' | 'area' | 'institucional'
    impacto               VARCHAR(20),

    fecha_limite_respuesta   TIMESTAMP,
    fecha_limite_resolucion  TIMESTAMP,
    fecha_primera_respuesta  TIMESTAMP,
    fecha_inicio_atencion    TIMESTAMP,
    fecha_resolucion         TIMESTAMP,
    fecha_limite_conformidad TIMESTAMP,
    fecha_conformidad        TIMESTAMP,
    conformidad_automatica   BOOLEAN     NOT NULL DEFAULT FALSE,
    fecha_cierre             TIMESTAMP,

    cantidad_reaperturas  SMALLINT     NOT NULL DEFAULT 0,
    reabierto             BOOLEAN      NOT NULL DEFAULT FALSE,

    calificacion          SMALLINT     CHECK (calificacion BETWEEN 1 AND 5),
    comentario_usuario    TEXT,

    created_at            TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN incidentes.id_ticket_origen IS
  'Ticket desde el que se originó el incidente al ser reclasificado por la mesa de servicios';
COMMENT ON COLUMN incidentes.impacto IS 'individual | area | institucional';


-- ------------------------------------------------------------
-- 6.2  APROBACIONES DE INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_aprobaciones (
    id_aprobacion    BIGSERIAL   PRIMARY KEY,
    id_incidente     BIGINT      NOT NULL REFERENCES incidentes(id_incidente),
    id_aprobador     BIGINT      NOT NULL REFERENCES trabajadores(id_trabajador),
    rol_aprobador    VARCHAR(30) NOT NULL,
    orden            SMALLINT    NOT NULL DEFAULT 1,
    -- 'pendiente' | 'aprobado' | 'rechazado'
    decision         VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    comentario       TEXT,
    fecha_decision   TIMESTAMP,
    fecha_limite     TIMESTAMP,
    created_at       TIMESTAMP   NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 6.3  MOVIMIENTOS DE INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_movimientos (
    id_movimiento        BIGSERIAL   PRIMARY KEY,
    id_incidente         BIGINT      NOT NULL REFERENCES incidentes(id_incidente),
    id_trabajador        BIGINT      NOT NULL REFERENCES trabajadores(id_trabajador),
    -- Igual que ticket_movimientos más 'diagnostico'
    tipo_movimiento      VARCHAR(30) NOT NULL,
    estado_anterior      VARCHAR(30),
    estado_nuevo         VARCHAR(30),
    id_tecnico_destino   BIGINT      REFERENCES trabajadores(id_trabajador),
    descripcion          TEXT,
    id_plantilla         BIGINT      REFERENCES plantilla_respuestas(id_plantilla),
    visible_solicitante  BOOLEAN     NOT NULL DEFAULT TRUE,
    created_at           TIMESTAMP   NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN incidente_movimientos.tipo_movimiento IS
  'registro | clasificacion | asignacion | inicio_atencion | diagnostico | avance |
   solicitud_info | respuesta_info | transferencia | resolucion |
   conformidad | conformidad_auto | reapertura | cancelacion | nota_interna';


-- ------------------------------------------------------------
-- 6.4  SOLICITUDES DE INFORMACIÓN — INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_solicitud_infos (
    id_solicitud_info      BIGSERIAL   PRIMARY KEY,
    id_incidente           BIGINT      NOT NULL REFERENCES incidentes(id_incidente),
    id_movimiento          BIGINT      NOT NULL REFERENCES incidente_movimientos(id_movimiento),
    id_tecnico             BIGINT      NOT NULL REFERENCES trabajadores(id_trabajador),
    pregunta               TEXT        NOT NULL,
    -- 'pendiente' | 'respondida' | 'vencida'
    estado                 VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    respuesta              TEXT,
    id_trabajador_responde BIGINT      REFERENCES trabajadores(id_trabajador),
    fecha_respuesta        TIMESTAMP,
    fecha_limite           TIMESTAMP,
    created_at             TIMESTAMP   NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 6.5  TRANSFERENCIAS — INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_transferencias (
    id_transferencia   BIGSERIAL PRIMARY KEY,
    id_incidente       BIGINT    NOT NULL REFERENCES incidentes(id_incidente),
    id_movimiento      BIGINT    NOT NULL REFERENCES incidente_movimientos(id_movimiento),
    id_tecnico_origen  BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),
    id_tecnico_destino BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),
    motivo             TEXT      NOT NULL,
    created_at         TIMESTAMP NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 6.6  ADJUNTOS — INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_adjuntos (
    id_adjunto         BIGSERIAL    PRIMARY KEY,
    id_incidente       BIGINT       NOT NULL REFERENCES incidentes(id_incidente),
    id_movimiento      BIGINT       REFERENCES incidente_movimientos(id_movimiento),
    id_solicitud_info  BIGINT       REFERENCES incidente_solicitud_infos(id_solicitud_info),
    id_trabajador      BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),
    nombre_archivo     VARCHAR(255) NOT NULL,
    ruta_almacen       VARCHAR(500) NOT NULL,
    tamano_bytes       BIGINT,
    tipo_mime          VARCHAR(100),
    -- 'todos' | 'solo_oti'
    visibilidad        VARCHAR(20)  NOT NULL DEFAULT 'todos',
    created_at         TIMESTAMP    NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 6.7  PAUSAS DE SLA — INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_pausa_slas (
    id_pausa             BIGSERIAL   PRIMARY KEY,
    id_incidente         BIGINT      NOT NULL REFERENCES incidentes(id_incidente),
    id_movimiento_inicio BIGINT      NOT NULL REFERENCES incidente_movimientos(id_movimiento),
    id_movimiento_fin    BIGINT      REFERENCES incidente_movimientos(id_movimiento),
    -- 'pendiente_info' | 'fuera_horario' | 'feriado'
    motivo_pausa         VARCHAR(30) NOT NULL,
    fecha_inicio_pausa   TIMESTAMP   NOT NULL,
    fecha_fin_pausa      TIMESTAMP,
    minutos_pausados     INT,
    created_at           TIMESTAMP   NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 6.8  DATOS DE CIERRE — INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_cierres (
    id_cierre                    BIGSERIAL PRIMARY KEY,
    id_incidente                 BIGINT    NOT NULL UNIQUE REFERENCES incidentes(id_incidente),
    id_tecnico                   BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),

    categoria_causa              VARCHAR(50)  NOT NULL,
    subcategoria_causa           VARCHAR(100),
    descripcion_causa            TEXT         NOT NULL,
    solucion_aplicada            TEXT         NOT NULL,
    tiempo_resolucion_minutos    INT,
    minutos_pausados_total       INT          NOT NULL DEFAULT 0,
    -- FK a conocimientos — se agrega con ALTER TABLE tras crear esa tabla
    id_conocimiento_usado        BIGINT,

    nivel_conocimiento_ti        SMALLINT     CHECK (nivel_conocimiento_ti BETWEEN 1 AND 5),
    prevenible_con_capacitacion  BOOLEAN      NOT NULL DEFAULT FALSE,
    tema_capacitacion_sugerido   VARCHAR(200),
    genera_articulo_conocimiento BOOLEAN      NOT NULL DEFAULT FALSE,
    observaciones                TEXT,
    created_at                   TIMESTAMP    NOT NULL DEFAULT NOW()
);


-- ------------------------------------------------------------
-- 6.9  CONFORMIDAD — INCIDENTE
-- ------------------------------------------------------------
CREATE TABLE incidente_conformidades (
    id_conformidad        BIGSERIAL   PRIMARY KEY,
    id_incidente          BIGINT      NOT NULL UNIQUE REFERENCES incidentes(id_incidente),
    id_movimiento         BIGINT      NOT NULL REFERENCES incidente_movimientos(id_movimiento),
    -- 'manual' | 'automatica'
    tipo                  VARCHAR(20) NOT NULL DEFAULT 'manual',
    -- 'conforme' | 'no_conforme'
    resultado             VARCHAR(20) NOT NULL,
    motivo_disconformidad TEXT,
    calificacion          SMALLINT    CHECK (calificacion BETWEEN 1 AND 5),
    comentario            TEXT,
    id_trabajador         BIGINT      REFERENCES trabajadores(id_trabajador),
    fecha_conformidad     TIMESTAMP   NOT NULL DEFAULT NOW()
);


-- ============================================================
-- 7. BASE DE CONOCIMIENTO Y AUTOSERVICIO
-- ============================================================

CREATE TABLE conocimientos (
    id_conocimiento      BIGSERIAL    PRIMARY KEY,
    titulo               VARCHAR(300) NOT NULL,
    descripcion_problema TEXT         NOT NULL,
    solucion             TEXT         NOT NULL,
    pasos_solucion       TEXT,
    id_servicio          BIGINT       REFERENCES servicios(id_servicio),
    id_autor             BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),
    id_ticket            BIGINT       REFERENCES tickets(id_ticket),
    id_incidente         BIGINT       REFERENCES incidentes(id_incidente),
    -- 'borrador' | 'publicado' | 'retirado'
    estado               VARCHAR(20)  NOT NULL DEFAULT 'borrador',
    permite_autoservicio BOOLEAN      NOT NULL DEFAULT FALSE,
    visitas              INT          NOT NULL DEFAULT 0,
    util_votos           INT          NOT NULL DEFAULT 0,
    no_util_votos        INT          NOT NULL DEFAULT 0,
    created_at           TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at           TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN conocimientos.pasos_solucion IS
  'Instrucciones paso a paso redactadas para usuarios no técnicos (autoservicio)';
COMMENT ON COLUMN conocimientos.permite_autoservicio IS
  'TRUE = el artículo se muestra al usuario cuando selecciona el servicio antes de abrir un ticket';

-- FK diferidas de cierre hacia conocimientos
ALTER TABLE ticket_cierres
    ADD CONSTRAINT fk_ticket_cierre_conocimiento
    FOREIGN KEY (id_conocimiento_usado) REFERENCES conocimientos(id_conocimiento);

ALTER TABLE incidente_cierres
    ADD CONSTRAINT fk_incidente_cierre_conocimiento
    FOREIGN KEY (id_conocimiento_usado) REFERENCES conocimientos(id_conocimiento);


-- Registro de consultas de autoservicio
CREATE TABLE autoservicio_consultas (
    id_consulta          BIGSERIAL PRIMARY KEY,
    id_trabajador        BIGINT    NOT NULL REFERENCES trabajadores(id_trabajador),
    id_conocimiento      BIGINT    NOT NULL REFERENCES conocimientos(id_conocimiento),
    id_servicio          BIGINT    REFERENCES servicios(id_servicio),
    abrio_ticket         BOOLEAN   NOT NULL DEFAULT FALSE,
    id_ticket_abierto    BIGINT    REFERENCES tickets(id_ticket),
    created_at           TIMESTAMP NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN autoservicio_consultas.abrio_ticket IS
  'TRUE = el artículo no resolvió el problema y el usuario abrió un ticket de todas formas';


-- ============================================================
-- 9. NOTIFICACIONES
-- ============================================================

CREATE TABLE notificaciones (
    id_notificacion          BIGSERIAL    PRIMARY KEY,
    id_trabajador            BIGINT       NOT NULL REFERENCES trabajadores(id_trabajador),
    -- Canal: 'correo' | 'portal' | 'sms'
    canal                    VARCHAR(20)  NOT NULL DEFAULT 'correo',

    id_ticket                BIGINT       REFERENCES tickets(id_ticket),
    id_incidente             BIGINT       REFERENCES incidentes(id_incidente),
    id_movimiento_ticket     BIGINT       REFERENCES ticket_movimientos(id_movimiento),
    id_movimiento_incidente  BIGINT       REFERENCES incidente_movimientos(id_movimiento),

    -- 'ticket_registrado' | 'ticket_asignado' | 'ticket_en_atencion' |
    -- 'solicitud_info' | 'ticket_resuelto' | 'conformidad_pendiente' |
    -- 'conformidad_automatica_pronto' | 'ticket_cerrado' |
    -- 'aprobacion_requerida' | 'ticket_transferido'
    tipo_evento              VARCHAR(50)  NOT NULL,

    asunto                   VARCHAR(300),
    cuerpo                   TEXT,

    -- 'pendiente' | 'enviado' | 'fallido' | 'rebotado'
    estado                   VARCHAR(20)  NOT NULL DEFAULT 'pendiente',
    intentos                 SMALLINT     NOT NULL DEFAULT 0,
    fecha_envio              TIMESTAMP,
    error_detalle            TEXT,

    created_at               TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON COLUMN notificaciones.tipo_evento IS
  'ticket_registrado | ticket_asignado | ticket_en_atencion |
   solicitud_info | ticket_resuelto | conformidad_pendiente |
   conformidad_automatica_pronto | ticket_cerrado |
   aprobacion_requerida | ticket_transferido';
COMMENT ON COLUMN notificaciones.estado IS
  'pendiente: en cola; enviado: entregado; fallido: error al enviar; rebotado: dirección inválida';


-- ============================================================
-- 10. REPORTES DE CAPACITACIÓN
-- ============================================================

CREATE TABLE reporte_capacitaciones (
    id_reporte                  BIGSERIAL   PRIMARY KEY,
    anio                        SMALLINT    NOT NULL,
    mes                         SMALLINT    CHECK (mes BETWEEN 1 AND 12),
    id_area                     BIGINT      REFERENCES areas(id_area),
    id_servicio                 BIGINT      REFERENCES servicios(id_servicio),

    total_tickets               INT         NOT NULL DEFAULT 0,
    tickets_por_conocimiento_ti INT         NOT NULL DEFAULT 0,
    tickets_prevenibles         INT         NOT NULL DEFAULT 0,

    -- Array JSON de strings con temas frecuentes
    temas_frecuentes            JSONB,
    nivel_conocimiento_promedio NUMERIC(3,2),

    created_at                  TIMESTAMP   NOT NULL DEFAULT NOW(),
    id_generado_por             BIGINT      REFERENCES trabajadores(id_trabajador),

    CONSTRAINT reporte_periodo_unico UNIQUE (anio, mes, id_area, id_servicio)
);

COMMENT ON COLUMN reporte_capacitaciones.temas_frecuentes IS
  'Array JSON con los temas de capacitación sugeridos más frecuentes en el período.
   Ejemplo: ["Uso de correo institucional", "Acceso al SIGA", "Gestión de contraseñas"]';


-- ============================================================
-- 11. AUDITORÍA Y SEGURIDAD
-- ============================================================

-- Tabla general de auditoría
CREATE TABLE auditoria_logs (
    id_log           BIGSERIAL    PRIMARY KEY,
    tabla            VARCHAR(100) NOT NULL,
    -- 'INSERT' | 'UPDATE' | 'DELETE'
    operacion        VARCHAR(10)  NOT NULL,
    id_registro      BIGINT       NOT NULL,
    -- Trabajador de la aplicación (id_trabajador)
    id_trabajador_app BIGINT      REFERENCES trabajadores(id_trabajador),
    usuario_bd       VARCHAR(100) NOT NULL DEFAULT current_user,
    ip_cliente       INET,
    valores_antes    JSONB,
    valores_despues  JSONB,
    hash_integridad  VARCHAR(64),
    created_at       TIMESTAMP    NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE auditoria_logs IS
  'Registro inmutable de cambios sobre tablas críticas del sistema.
   Nunca debe permitirse UPDATE ni DELETE sobre esta tabla.';
COMMENT ON COLUMN auditoria_logs.hash_integridad IS
  'Hash SHA-256 del contenido del registro para detectar manipulación directa en la base de datos';


-- ============================================================
-- 12. VISTAS Y VISTAS MATERIALIZADAS
-- ============================================================

CREATE VIEW v_ticket_resumen AS
SELECT
    t.id_ticket,
    t.codigo,
    t.estado,
    t.titulo,
    t.clasificacion,
    t.prioridad,
    t.canal_ingreso,
    t.created_at,
    t.fecha_limite_resolucion,
    t.fecha_resolucion,
    t.fecha_cierre,
    t.cantidad_reaperturas,
    t.calificacion,
    -- Solicitante
    ts.nombres || ' ' || ts.apellidos AS solicitante,
    us.email                           AS correo_solicitante,
    a.nombre                           AS area_solicitante,
    -- Técnico
    tt.nombres || ' ' || tt.apellidos AS tecnico_asignado,
    -- Servicio
    sv.nombre                          AS servicio,
    cs.nombre                          AS categoria_servicio,
    -- SLA
    sl.nombre                          AS sla,
    sl.tiempo_resolucion_h,
    CASE
        WHEN t.fecha_resolucion IS NOT NULL AND t.fecha_limite_resolucion IS NOT NULL
        THEN t.fecha_resolucion <= t.fecha_limite_resolucion
        ELSE NULL
    END AS cumplio_sla,
    CASE
        WHEN t.fecha_resolucion IS NOT NULL
        THEN EXTRACT(EPOCH FROM (t.fecha_resolucion - t.created_at)) / 60
             - COALESCE(tc.minutos_pausados_total, 0)
        ELSE NULL
    END AS minutos_resolucion_efectivos
FROM tickets t
LEFT JOIN trabajadores  ts ON t.id_solicitante        = ts.id_trabajador
LEFT JOIN users          us ON ts.user_id              = us.id
LEFT JOIN areas           a ON ts.id_area              = a.id_area
LEFT JOIN trabajadores  tt ON t.id_tecnico_asignado   = tt.id_trabajador
LEFT JOIN servicios     sv ON t.id_servicio            = sv.id_servicio
LEFT JOIN categoria_servicios cs ON sv.id_categoria   = cs.id_categoria
LEFT JOIN slas          sl ON t.id_sla                = sl.id_sla
LEFT JOIN ticket_cierres tc ON t.id_ticket            = tc.id_ticket;


CREATE MATERIALIZED VIEW mv_metricas_ticket AS
SELECT
    sv.id_servicio,
    sv.nombre                              AS servicio,
    cs.nombre                              AS categoria,
    tt.id_trabajador                       AS id_tecnico,
    tt.nombres || ' ' || tt.apellidos     AS tecnico,
    DATE_TRUNC('month', t.created_at)     AS mes,

    COUNT(*)                               AS total_tickets,
    COUNT(*) FILTER (WHERE t.estado = 'cerrado')   AS tickets_cerrados,
    COUNT(*) FILTER (WHERE t.estado = 'cancelado') AS tickets_cancelados,

    COUNT(*) FILTER (
        WHERE t.fecha_resolucion IS NOT NULL
          AND t.fecha_limite_resolucion IS NOT NULL
          AND t.fecha_resolucion <= t.fecha_limite_resolucion
    )                                      AS tickets_en_sla,

    AVG(
        CASE WHEN t.fecha_resolucion IS NOT NULL
        THEN EXTRACT(EPOCH FROM (t.fecha_resolucion - t.created_at)) / 60
             - COALESCE(tc.minutos_pausados_total, 0)
        END
    )                                      AS minutos_resolucion_promedio,

    SUM(t.cantidad_reaperturas)            AS total_reaperturas,
    AVG(t.calificacion)                    AS calificacion_promedio,
    AVG(tc.nivel_conocimiento_ti)          AS nivel_conocimiento_promedio,
    COUNT(*) FILTER (WHERE tc.prevenible_con_capacitacion = TRUE) AS tickets_prevenibles

FROM tickets t
LEFT JOIN trabajadores  tt ON t.id_tecnico_asignado = tt.id_trabajador
LEFT JOIN servicios     sv ON t.id_servicio         = sv.id_servicio
LEFT JOIN categoria_servicios cs ON sv.id_categoria = cs.id_categoria
LEFT JOIN ticket_cierres tc ON t.id_ticket          = tc.id_ticket
GROUP BY sv.id_servicio, sv.nombre, cs.nombre,
         tt.id_trabajador, tt.nombres, tt.apellidos,
         DATE_TRUNC('month', t.created_at)
WITH NO DATA;

CREATE INDEX idx_mv_mes      ON mv_metricas_ticket(mes);
CREATE INDEX idx_mv_servicio ON mv_metricas_ticket(id_servicio);
CREATE INDEX idx_mv_tecnico  ON mv_metricas_ticket(id_tecnico);
-- Para refrescar: REFRESH MATERIALIZED VIEW CONCURRENTLY mv_metricas_ticket;


-- ============================================================
-- 13. TRIGGERS DE AUDITORÍA (PostgreSQL)
-- ============================================================

CREATE OR REPLACE FUNCTION fn_auditoria()
RETURNS TRIGGER
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
DECLARE
    v_id_trabajador_app BIGINT;
    v_ip_cliente        INET;
    v_id_registro       BIGINT;
    v_antes             JSONB;
    v_despues           JSONB;
BEGIN
    BEGIN
        v_id_trabajador_app := current_setting('app.id_trabajador', TRUE)::BIGINT;
    EXCEPTION WHEN OTHERS THEN
        v_id_trabajador_app := NULL;
    END;

    BEGIN
        v_ip_cliente := current_setting('app.ip_cliente', TRUE)::INET;
    EXCEPTION WHEN OTHERS THEN
        v_ip_cliente := NULL;
    END;

    IF TG_OP = 'DELETE' THEN
        v_id_registro := OLD.id_ticket;
        v_antes       := to_jsonb(OLD);
        v_despues     := NULL;
    ELSIF TG_OP = 'INSERT' THEN
        v_id_registro := NEW.id_ticket;
        v_antes       := NULL;
        v_despues     := to_jsonb(NEW);
    ELSE
        v_id_registro := NEW.id_ticket;
        v_antes       := to_jsonb(OLD);
        v_despues     := to_jsonb(NEW);
    END IF;

    INSERT INTO auditoria_logs (
        tabla, operacion, id_registro,
        id_trabajador_app, usuario_bd, ip_cliente,
        valores_antes, valores_despues
    ) VALUES (
        TG_TABLE_NAME, TG_OP, v_id_registro,
        v_id_trabajador_app, current_user, v_ip_cliente,
        v_antes, v_despues
    );

    RETURN COALESCE(NEW, OLD);
END;
$$;

COMMENT ON FUNCTION fn_auditoria() IS
  'Función genérica de auditoría. La aplicación debe llamar:
   SET LOCAL app.id_trabajador = <id>;
   SET LOCAL app.ip_cliente = <ip>;
   al inicio de cada transacción.';

CREATE TRIGGER trg_auditoria_ticket
AFTER INSERT OR UPDATE OR DELETE ON tickets
FOR EACH ROW EXECUTE FUNCTION fn_auditoria();

CREATE OR REPLACE FUNCTION fn_auditoria_movimiento()
RETURNS TRIGGER LANGUAGE plpgsql SECURITY DEFINER AS $$
DECLARE
    v_id_trabajador_app BIGINT;
    v_ip_cliente        INET;
BEGIN
    BEGIN v_id_trabajador_app := current_setting('app.id_trabajador', TRUE)::BIGINT;
    EXCEPTION WHEN OTHERS THEN v_id_trabajador_app := NULL; END;
    BEGIN v_ip_cliente := current_setting('app.ip_cliente', TRUE)::INET;
    EXCEPTION WHEN OTHERS THEN v_ip_cliente := NULL; END;

    INSERT INTO auditoria_logs (
        tabla, operacion, id_registro,
        id_trabajador_app, usuario_bd, ip_cliente,
        valores_antes, valores_despues
    ) VALUES (
        TG_TABLE_NAME, TG_OP,
        COALESCE(NEW.id_movimiento, OLD.id_movimiento),
        v_id_trabajador_app, current_user, v_ip_cliente,
        CASE WHEN TG_OP != 'INSERT' THEN to_jsonb(OLD) END,
        CASE WHEN TG_OP != 'DELETE' THEN to_jsonb(NEW) END
    );
    RETURN COALESCE(NEW, OLD);
END;
$$;

CREATE TRIGGER trg_auditoria_ticket_movimiento
AFTER INSERT OR UPDATE OR DELETE ON ticket_movimientos
FOR EACH ROW EXECUTE FUNCTION fn_auditoria_movimiento();

CREATE OR REPLACE FUNCTION fn_auditoria_incidente()
RETURNS TRIGGER LANGUAGE plpgsql SECURITY DEFINER AS $$
DECLARE
    v_id_trabajador_app BIGINT;
    v_ip_cliente        INET;
BEGIN
    BEGIN v_id_trabajador_app := current_setting('app.id_trabajador', TRUE)::BIGINT;
    EXCEPTION WHEN OTHERS THEN v_id_trabajador_app := NULL; END;
    BEGIN v_ip_cliente := current_setting('app.ip_cliente', TRUE)::INET;
    EXCEPTION WHEN OTHERS THEN v_ip_cliente := NULL; END;

    INSERT INTO auditoria_logs (
        tabla, operacion, id_registro,
        id_trabajador_app, usuario_bd, ip_cliente,
        valores_antes, valores_despues
    ) VALUES (
        TG_TABLE_NAME, TG_OP,
        COALESCE(NEW.id_incidente, OLD.id_incidente),
        v_id_trabajador_app, current_user, v_ip_cliente,
        CASE WHEN TG_OP != 'INSERT' THEN to_jsonb(OLD) END,
        CASE WHEN TG_OP != 'DELETE' THEN to_jsonb(NEW) END
    );
    RETURN COALESCE(NEW, OLD);
END;
$$;

CREATE TRIGGER trg_auditoria_incidente
AFTER INSERT OR UPDATE OR DELETE ON incidentes
FOR EACH ROW EXECUTE FUNCTION fn_auditoria_incidente();

CREATE OR REPLACE FUNCTION fn_set_updated_at()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_updated_ticket
BEFORE UPDATE ON tickets FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

CREATE TRIGGER trg_updated_incidente
BEFORE UPDATE ON incidentes FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

CREATE TRIGGER trg_updated_trabajador
BEFORE UPDATE ON trabajadores FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

CREATE TRIGGER trg_updated_activo
BEFORE UPDATE ON activos_ti FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

CREATE TRIGGER trg_updated_conocimiento
BEFORE UPDATE ON conocimientos FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();


-- ============================================================
-- 14. ÍNDICES
-- ============================================================

-- Tickets
CREATE INDEX idx_ticket_estado             ON tickets(estado);
CREATE INDEX idx_ticket_solicitante        ON tickets(id_solicitante);
CREATE INDEX idx_ticket_tecnico            ON tickets(id_tecnico_asignado);
CREATE INDEX idx_ticket_agente_mesa        ON tickets(id_agente_mesa);
CREATE INDEX idx_ticket_servicio           ON tickets(id_servicio);
CREATE INDEX idx_ticket_prioridad          ON tickets(prioridad);
CREATE INDEX idx_ticket_clasificacion      ON tickets(clasificacion);
CREATE INDEX idx_ticket_limite_resolucion  ON tickets(fecha_limite_resolucion);
CREATE INDEX idx_ticket_limite_conformidad ON tickets(fecha_limite_conformidad);
CREATE INDEX idx_ticket_activo             ON tickets(id_activo);
CREATE INDEX idx_ticket_creado_en          ON tickets(created_at);

-- Movimientos de ticket
CREATE INDEX idx_tmov_ticket  ON ticket_movimientos(id_ticket);
CREATE INDEX idx_tmov_tipo    ON ticket_movimientos(tipo_movimiento);
CREATE INDEX idx_tmov_worker  ON ticket_movimientos(id_trabajador);
CREATE INDEX idx_tmov_fecha   ON ticket_movimientos(created_at);

-- Aprobaciones de ticket
CREATE INDEX idx_tapro_ticket    ON ticket_aprobaciones(id_ticket);
CREATE INDEX idx_tapro_aprobador ON ticket_aprobaciones(id_aprobador);
CREATE INDEX idx_tapro_decision  ON ticket_aprobaciones(decision);

-- Solicitudes de información de ticket
CREATE INDEX idx_tsol_ticket ON ticket_solicitud_infos(id_ticket);
CREATE INDEX idx_tsol_estado ON ticket_solicitud_infos(estado);

-- Transferencias de ticket
CREATE INDEX idx_ttrans_ticket  ON ticket_transferencias(id_ticket);
CREATE INDEX idx_ttrans_destino ON ticket_transferencias(id_tecnico_destino);

-- Adjuntos de ticket
CREATE INDEX idx_tadj_ticket     ON ticket_adjuntos(id_ticket);
CREATE INDEX idx_tadj_movimiento ON ticket_adjuntos(id_movimiento);

-- Pausas SLA ticket
CREATE INDEX idx_tpausa_ticket ON ticket_pausa_slas(id_ticket);

-- Cierre de ticket
CREATE INDEX idx_tcierre_categoria   ON ticket_cierres(categoria_causa);
CREATE INDEX idx_tcierre_nivel       ON ticket_cierres(nivel_conocimiento_ti);
CREATE INDEX idx_tcierre_capacitacion ON ticket_cierres(prevenible_con_capacitacion);
CREATE INDEX idx_tcierre_conocimiento ON ticket_cierres(genera_articulo_conocimiento);

-- Incidentes
CREATE INDEX idx_inc_estado             ON incidentes(estado);
CREATE INDEX idx_inc_servicio           ON incidentes(id_servicio_afectado);
CREATE INDEX idx_inc_tecnico            ON incidentes(id_tecnico_asignado);
CREATE INDEX idx_inc_prioridad          ON incidentes(prioridad);
CREATE INDEX idx_inc_ticket_origen      ON incidentes(id_ticket_origen);
CREATE INDEX idx_inc_limite_resolucion  ON incidentes(fecha_limite_resolucion);
CREATE INDEX idx_inc_limite_conformidad ON incidentes(fecha_limite_conformidad);
CREATE INDEX idx_inc_activo             ON incidentes(id_activo);
CREATE INDEX idx_inc_creado_en          ON incidentes(created_at);

-- Movimientos de incidente
CREATE INDEX idx_imov_incidente ON incidente_movimientos(id_incidente);
CREATE INDEX idx_imov_tipo      ON incidente_movimientos(tipo_movimiento);
CREATE INDEX idx_imov_worker    ON incidente_movimientos(id_trabajador);

-- Adjuntos de incidente
CREATE INDEX idx_iadj_incidente ON incidente_adjuntos(id_incidente);

-- Base de conocimiento
CREATE INDEX idx_conoc_servicio     ON conocimientos(id_servicio);
CREATE INDEX idx_conoc_estado       ON conocimientos(estado);
CREATE INDEX idx_conoc_autoservicio ON conocimientos(permite_autoservicio);

-- Autoservicio
CREATE INDEX idx_autoserv_trabajador   ON autoservicio_consultas(id_trabajador);
CREATE INDEX idx_autoserv_conocimiento ON autoservicio_consultas(id_conocimiento);
CREATE INDEX idx_autoserv_abrio_ticket ON autoservicio_consultas(abrio_ticket);

-- Notificaciones
CREATE INDEX idx_notif_trabajador  ON notificaciones(id_trabajador);
CREATE INDEX idx_notif_ticket      ON notificaciones(id_ticket);
CREATE INDEX idx_notif_incidente   ON notificaciones(id_incidente);
CREATE INDEX idx_notif_estado      ON notificaciones(estado);
CREATE INDEX idx_notif_tipo_evento ON notificaciones(tipo_evento);

-- Auditoría
CREATE INDEX idx_audit_tabla      ON auditoria_logs(tabla);
CREATE INDEX idx_audit_operacion  ON auditoria_logs(operacion);
CREATE INDEX idx_audit_id_registro ON auditoria_logs(id_registro);
CREATE INDEX idx_audit_trabajador ON auditoria_logs(id_trabajador_app);
CREATE INDEX idx_audit_fecha      ON auditoria_logs(created_at);

-- Activos TI
CREATE INDEX idx_activo_tipo     ON activos_ti(tipo);
CREATE INDEX idx_activo_estado   ON activos_ti(estado);
CREATE INDEX idx_activo_area     ON activos_ti(id_area);
CREATE INDEX idx_activo_trabajador ON activos_ti(id_trabajador_asignado);

-- Trabajadores y servicios
CREATE INDEX idx_trabajador_area     ON trabajadores(id_area);
CREATE INDEX idx_servicio_categoria  ON servicios(id_categoria);

-- Horario y feriados
CREATE INDEX idx_horario_dia  ON horarios_laborales(dia_semana);
CREATE INDEX idx_feriado_fecha ON feriados(fecha);


-- ============================================================
-- 15. DATOS SEMILLA
-- ============================================================

-- SLA
INSERT INTO slas (nombre, prioridad, tiempo_respuesta_h, tiempo_resolucion_h, dias_conformidad_automatica) VALUES
    ('Crítico',  1,  1,   4,  3),
    ('Alto',     2,  2,   8,  3),
    ('Medio',    3,  4,  24,  3),
    ('Bajo',     4,  8,  72,  3);

-- Horario laboral (lunes a viernes, 8:00–13:00 y 14:00–17:00)
INSERT INTO horarios_laborales (dia_semana, hora_inicio, hora_fin) VALUES
    (1, '08:00', '13:00'), (1, '14:00', '17:00'),
    (2, '08:00', '13:00'), (2, '14:00', '17:00'),
    (3, '08:00', '13:00'), (3, '14:00', '17:00'),
    (4, '08:00', '13:00'), (4, '14:00', '17:00'),
    (5, '08:00', '13:00'), (5, '14:00', '17:00');

-- Feriados nacionales Perú 2025
INSERT INTO feriados (fecha, nombre, tipo) VALUES
    ('2025-01-01', 'Año Nuevo',                 'nacional'),
    ('2025-04-17', 'Jueves Santo',              'nacional'),
    ('2025-04-18', 'Viernes Santo',             'nacional'),
    ('2025-05-01', 'Día del Trabajo',           'nacional'),
    ('2025-06-07', 'Batalla de Arica',          'nacional'),
    ('2025-06-29', 'San Pedro y San Pablo',     'nacional'),
    ('2025-07-28', 'Fiestas Patrias',           'nacional'),
    ('2025-07-29', 'Fiestas Patrias',           'nacional'),
    ('2025-08-30', 'Santa Rosa de Lima',        'nacional'),
    ('2025-10-08', 'Combate de Angamos',        'nacional'),
    ('2025-11-01', 'Todos los Santos',          'nacional'),
    ('2025-12-08', 'Inmaculada Concepción',     'nacional'),
    ('2025-12-25', 'Navidad',                   'nacional');

-- Categorías de servicio
INSERT INTO categoria_servicios (nombre) VALUES
    ('Servicios de Estación de Trabajo'),
    ('Servicios de Comunicación'),
    ('Servicios de Conectividad'),
    ('Servicios de Acceso e Identidad'),
    ('Servicios de Almacenamiento y Archivos'),
    ('Servicios de Aplicaciones Institucionales'),
    ('Servicios de Digitalización y Documento Electrónico'),
    ('Servicios de Capacitación TI');

-- Servicios del catálogo
INSERT INTO servicios (id_categoria, nombre, tipo, requiere_aprobacion, tipo_aprobador, id_sla_defecto) VALUES
    (1, 'Provisión de equipo de cómputo',          'solicitud', TRUE,  'ambos',          3),
    (1, 'Instalación o actualización de software', 'solicitud', FALSE, NULL,              3),
    (1, 'Reparación o reemplazo de equipo',        'solicitud', FALSE, NULL,              2),
    (1, 'Configuración de impresora o escáner',    'solicitud', FALSE, NULL,              4),
    (2, 'Creación de cuenta de correo institucional',   'solicitud', TRUE,  'supervisor_oti', 3),
    (2, 'Acceso a plataforma de videoconferencia',      'solicitud', FALSE, NULL,              4),
    (2, 'Acceso a directorio telefónico interno (VoIP)','solicitud', FALSE, NULL,              4),
    (2, 'Acceso a la intranet municipal',               'solicitud', FALSE, NULL,              4),
    (3, 'Acceso a internet',                        'solicitud', FALSE, NULL,              3),
    (3, 'Acceso a la red institucional (LAN/WiFi)', 'solicitud', FALSE, NULL,              3),
    (3, 'Acceso remoto a la red (VPN)',             'solicitud', TRUE,  'supervisor_oti', 3),
    (4, 'Creación de cuenta de usuario',             'solicitud', TRUE,  'ambos',          2),
    (4, 'Restablecimiento de contraseña',            'solicitud', FALSE, NULL,              2),
    (4, 'Solicitud de acceso a sistema o aplicativo','solicitud', TRUE,  'jefe_area',      3),
    (4, 'Modificación o baja de cuenta de usuario',  'solicitud', TRUE,  'supervisor_oti', 2),
    (5, 'Almacenamiento compartido en red',                 'solicitud', FALSE, NULL,          4),
    (5, 'Recuperación de archivos desde respaldo',          'solicitud', FALSE, NULL,          2),
    (5, 'Solicitud de espacio adicional de almacenamiento', 'solicitud', TRUE,  'jefe_area',  4),
    (6, 'Acceso al SIGA',                              'solicitud', TRUE, 'ambos',          2),
    (6, 'Acceso al SIAF',                              'solicitud', TRUE, 'ambos',          2),
    (6, 'Acceso al sistema de Trámite Documentario',   'solicitud', TRUE, 'jefe_area',      3),
    (6, 'Acceso al sistema de Planillas / RRHH',       'solicitud', TRUE, 'ambos',          2),
    (6, 'Acceso al sistema de Recaudación Tributaria', 'solicitud', TRUE, 'ambos',          2),
    (7, 'Digitalización de documentos físicos',     'solicitud', FALSE, NULL,              4),
    (7, 'Obtención de firma digital institucional', 'solicitud', TRUE,  'supervisor_oti', 3),
    (7, 'Soporte al expediente electrónico',        'solicitud', FALSE, NULL,              3),
    (8, 'Capacitación en ofimática',                   'solicitud', FALSE, NULL,          4),
    (8, 'Capacitación en aplicativos institucionales', 'solicitud', FALSE, NULL,          4),
    (8, 'Inducción TI para personal nuevo',            'solicitud', TRUE,  'jefe_area',  3);

-- Roles Spatie (ejecutar desde Laravel: php artisan db:seed)
-- Role::create(['name' => 'solicitante']);
-- Role::create(['name' => 'tecnico']);
-- Role::create(['name' => 'supervisor']);
-- Role::create(['name' => 'administrador']);

-- Plantillas de respuesta iniciales
INSERT INTO plantilla_respuestas (nombre, tipo_movimiento, contenido) VALUES
    (
        'Confirmación de recepción', 'registro',
        'Estimado/a {{nombre_usuario}}, hemos recibido su solicitud con el código {{codigo_ticket}}. Un profesional de TI la atenderá dentro del plazo establecido. Puede hacer seguimiento desde el portal institucional.'
    ),
    (
        'Solicitud de información general', 'solicitud_info',
        'Estimado/a {{nombre_usuario}}, para continuar con la atención de su solicitud {{codigo_ticket}} necesitamos la siguiente información: {{descripcion}}. Por favor responda a la brevedad posible.'
    ),
    (
        'Notificación de resolución', 'resolucion',
        'Estimado/a {{nombre_usuario}}, su solicitud {{codigo_ticket}} ha sido resuelta. {{descripcion}}. Tiene 3 días laborables para confirmar su conformidad desde el portal. De no recibir respuesta, se considerará conforme automáticamente.'
    ),
    (
        'Restablecimiento de contraseña completado', 'resolucion',
        'Estimado/a {{nombre_usuario}}, su contraseña ha sido restablecida correctamente. Por seguridad le recomendamos cambiarla al primer inicio de sesión. Código de atención: {{codigo_ticket}}.'
    );
