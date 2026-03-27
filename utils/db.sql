create table dependencias
(
    id              bigserial
        constraint unidades_pk
            primary key,
    nombre          varchar(200),
    abr             varchar(10),
    activo          boolean default true not null,
    municaj_area_id bigint,
    created_at      timestamp(0),
    updated_at      timestamp(0)
);
INSERT INTO dependencias (nombre, abr, activo, municaj_area_id)
VALUES ('Alcaldía', 'A', true, 2),
       ('Concejo Municipal', 'CM', true, 104),
       ('Gerencia de Desarrollo Ambiental', 'GDA', true, 57),
       ('Gerencia de Desarrollo Económico', 'GDE', true, 41),
       ('Gerencia de Desarrollo Social y Humano', 'GDSH', true, 136),
       ('Gerencia de Desarrollo Territorial y Urbano', 'GDTU', true, 125),
       ('Gerencia de Infraestructura Pública', 'GIP', true, 121),
       ('Gerencia de Seguridad Ciudadana', 'GSC', true, 61),
       ('Gerencia de Transportes y Seguridad Vial', 'GTSV', true, 128),
       ('Gerencia de Turismo y Cultura', 'GTC', true, 142),
       ('Gerencia Municipal', 'GM', true, 9),
       ('Oficina de Abastecimiento y Control Patrimonial', 'OACP', true, 114),
       ('Oficina de Contabilidad', 'OC', true, 115),
       ('Oficina de Mejor Atención al Ciudadano', 'OMAC', true, 110),
       ('Oficina de Planeamiento y Modernización', 'OPM', true, 118),
       ('Oficina de Presupuesto', 'OP', true, 119),
       ('Oficina de Programación Multianual de Inversiones', 'OPMI', true, 120),
       ('Oficina de Remuneraciones y Control de Personal', 'ORCP', true, 112),
       ('Oficina de Tecnologías de la Información', 'OTI', true, 117),
       ('Oficina de Tesorería', 'OT', true, 116),
       ('Oficina General de Administración y Finanzas', 'OGAF', true, 113),
       ('Oficina General de Asesoría Jurídica', 'OGAJ', true, 11),
       ('Oficina General de Gestión de Recursos Humanos', 'OGGRRHH', true, 24),
       ('Oficina General de Gestión Documentaria y Atención al Ciudadano', 'OGGDAC', true, 109),
       ('Oficina General de Imagen y Comunicaciones Institucionales', 'OGICI', true, 111),
       ('Oficina General de Planeamiento y Presupuesto', 'OGPP', true, 12),
       ('Procuraduría Pública', 'PP', true, 106),
       ('Subgerencia de Comercialización y Licencias', 'SCL', true, 132),
       ('Subgerencia de Educación Recreación y Deporte', 'SERD', true, 139),
       ('Subgerencia de Ejecución de Inversiones', 'SEI', true, 123),
       ('Subgerencia de Formulación de Inversiones', 'SFI', true, 122),
       ('Subgerencia de Gestión Ambiental', 'SGA', true, 133),
       ('Subgerencia de Gestión de Riesgo y Desastres', 'SGRD', true, 141),
       ('Subgerencia de Gestión Integral de Residuos Sólidos', 'SGIRS', true, 134),
       ('Subgerencia de Inspección y Seguridad Vial', 'SISV', true, 130),
       ('Subgerencia de Licencias y Permisos Urbanos ', 'SLPU', true, 127),
       ('Subgerencia de Mantenimiento y Gestión de Caminos', 'SMGC', true, 124),
       ('Subgerencia de Participación Vecinal y Registro Civil', 'SPVRC', true, 138),
       ('Subgerencia de Planificación Territorial y Centro Histórico', 'SPTCH', true, 126),
       ('Subgerencia de Productividad y Promoción de la Inversión', 'SPPI', true, 131),
       ('Subgerencia de Programas Sociales y Empadronamiento', 'SPSE', true, 137),
       ('Subgerencia de Regulación y Autorizaciones de Transporte', 'SRAT', true, 129),
       ('Subgerencia de Salud', 'SSA', true, 54),
       ('Subgerencia de Saneamiento Básico', 'SSB', true, 135),
       ('Subgerencia de Serenazgo', 'SSE', true, 140);



create table trabajadores
(
    id              bigserial
        primary key,
    dependencias_id bigint                not null
        constraint trabajadores_dependencias_id_fk
            references dependencias,
    dni             varchar(8)            not null
        constraint trabajadores_dni_unique
            unique,
    paterno         varchar(100),
    materno         varchar(100),
    nombres         varchar(100),

    es_jefe         boolean default false not null,
    voluntario      boolean default false not null,

    ut_id           bigint,
    persona_id      bigint,

    created_at      timestamp(0),
    updated_at      timestamp(0)
);
INSERT INTO trabajadores (unidad_id, dni, paterno, materno, nombres, es_jefe, voluntario)
VALUES (14, '54320001', 'GARCIA', 'RODRIGUEZ', 'JUAN', true, false),
       (21, '54320002', 'RODRIGUEZ', 'LOPEZ', 'MARTIN', true, false),
       (14, '54320003', 'RIOS', 'VELA', 'ALVARO', false, false),
       (14, '54320004', 'CARRASCAL', 'SIFUENTES', 'DELICIA AZUCENA', false, false),
       (14, '54320005', 'LOPEZ', 'SILVESTRE', 'JESSICA DENISSE', false, false),
       (14, '54320006', 'CHAVARRIA', 'VELASQUEZ', 'LISBETH', false, false),
       (14, '54320007', 'SINTI', 'CRUZ', 'VIRIDIANA', false, true),
       (21, '54320008', 'ABRAMONTE', 'SAAVEDRA', 'CARMEN', false, false),
       (21, '54320009', 'SANDOVAL', 'LOZANO', 'LEOCADIA', false, false),
       (21, '54320010', 'GUERREIRO', 'TORRES', 'GLADYS', false, false),
       (21, '54320011', 'MERA', 'PUGA', 'OFELIA', false, false),
       (21, '54320012', 'VELASCO', 'AGUILAR', 'ANGELICA YSABEL', false, false),
       (19, '54320013', 'LEZAMA', 'BAZAN', 'JORGE', true, false),
       (19, '54320014', 'FLORES', 'RENGIFO', 'FELIPE', false, false),
       (19, '54320015', 'RIOS', 'VILLACORTA', 'AMERICO', false, false),
       (19, '54320016', 'VARGAS', 'BARBARAN', 'FELICIANO', false, false),
       (19, '54320017', 'PACAYA', 'CACHIQUE', 'ANDRES', false, false),
       (19, '54320018', 'MIRES', 'BORGES', 'MIGUEL', false, false),
       (19, '54320019', 'VIENA', 'BANEO', 'WILLIAM', false, false),
       (19, '54320020', 'DOMINGUEZ', 'HINOSTROZA', 'AMERICO', false, false),
       (19, '54320021', 'PINEDO', 'PAREDES', 'EDWIN', false, true),
       (19, '54320022', 'MAYNAS', 'CHOLITA', 'LUCIANO', false, true),
       (19, '54320023', 'TUESTA', 'RUIZ', 'FERNANDO', false, true);



create table profesionales
(
    id            bigserial
        primary key,

    trabajador_id bigint
        constraint profesionales_trabajadores_id_fk
            references trabajadores,

    activo        boolean default true not null,

    created_at    timestamp(0),
    updated_at    timestamp(0)
);
INSERT INTO profesionales (trabajador_id, equipo_id)
VALUES (13, 1),
       (14, 1),
       (15, 1),
       (16, 1),
       (17, 2),
       (18, 2),
       (19, 2),
       (20, 2),
       (21, 1),
       (22, 1),
       (23, 2);



/*
create table elementos
(
    id          bigserial
        constraint elementos_pk
            primary key,
    grupo       text                 not null,
    codigo      text                 not null,
    nombre      text                 not null,
    label       text                 not null,
    descripcion text,
    valor       integer              not null,
    orden       integer              not null,
    activo      boolean default true not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);
*/

-- Mide qué tan rápido debe resolverse el problema.
create table urgencias
(
    id          bigserial
        constraint urgencias_pk
            primary key,

    nombre      varchar(50)          not null,
    label       varchar(50)          not null,
    descripcion text,
    valor       integer              not null,
    orden       integer              not null,
    activo      boolean default true not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);
INSERT INTO urgencias (id, nombre, label, valor, orden, accesibleUsuarios)
VALUES (1, 'BAJA', 'Baja', 1, 1, true),   -- SOLICITUD
       (2, 'MEDIA', 'Media', 2, 2, true), -- INCIDENTE-1
       (3, 'ALTA', 'Alta', 3, 3, false);
-- INCIDENTE-2

-- Alta	    El trabajo está completamente detenido
-- Media	El trabajo continúa pero con dificultad
-- Baja	    Puede esperar


-- Mide a cuántos usuarios o servicios afecta el problema.
create table impactos
(
    id          bigserial
        constraint impactos_pk
            primary key,

    nombre      varchar(50)          not null,
    label       varchar(50)          not null,
    descripcion text,
    valor       integer              not null,
    orden       integer              not null,
    activo      boolean default true not null,
    -- accesibleUsuarios boolean default false not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);


INSERT INTO impactos (id, nombre, label, valor, orden)
VALUES (1, 'BAJO', 'Bajo', 1, 1),   -- TRABAJADOR-SOLICITUD, TRABAJADOR-INCIDENTE-1
       (2, 'MEDIO', 'Medio', 2, 2), -- TRABAJADOR-INCIDENTE-2, JEFE-SOLICITUD
       (3, 'CRITICO', 'Crítico', 3, 3);
-- JEFE-INCIDENTE-1-2

-- Alto	    Toda la municipalidad o sistema crítico caído
-- Medio	Un área completa no puede trabajar
-- Bajo	    Solo afecta a un usuario


/*

TIPO
SOLICITUD - U1
    - TRABAJADOR I1 -> BAJA P1
    - JEFE I2-> MEDIA P2

INCIDENTE - NO MONITOREADO - U2
    - TRABAJADOR I2-> MEDIA P2
    - JEFE I3-> ALTA P3

INCIDENTE - MONITOREADO - U3
    - TRABAJADOR I2-> ALTA P3
    - JEFE I3 -> ALTA P3
*/



create table prioridades
(
    id          bigserial
        constraint prioridades_pk
            primary key,

    nombre      varchar(50)          not null,
    label       varchar(50)          not null,
    descripcion text,
    valor       integer              not null,
    orden       integer              not null,
    activo      boolean default true not null,

    calculo     text,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);
INSERT INTO prioridades (id, nombre, label, valor, orden, calculo)
VALUES (1, 'BAJA', 'Baja', 1, 1, '1x1|2x1|1x2'),   -- 1x1|
       (2, 'MEDIA', 'Media', 2, 2, '1x3|2x2|3x1'), -- 1x2|2x2|
       (3, 'CRITICA', 'Crítica', 3, 3, '2x3|3x2|3x3'); -- 2x3|3x2|3x3

create table tipos
(
    id                      bigserial
        constraint tipos_pk
            primary key,

    nombre                  varchar(100)          not null,
    label                   varchar(100)          not null,
    descripcion             text,
    activo                  boolean default true  not null,
    accesible_mesa_servicio boolean default false not null,
    created_at              timestamp(0),
    updated_at              timestamp(0)
);

INSERT INTO tipos (id, nombre, label, accesible_mesa_servicio)
VALUES (1, 'SOLICITUD', 'Solicitud', true),
       (2, 'INCIDENTE', 'Incidente', true),
       (3, 'CAMBIO', 'Cambio', false),
       (4, 'PROBLEMA', 'Problema', false),
       (5, 'EVENTO', 'Evento', false);



create table categorias
(
    id          bigserial
        constraint categorias_pk
            primary key,

    nombre      varchar(100)         not null,
    label       varchar(100)         not null,
    descripcion text,
    activo      boolean default true not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);

INSERT INTO categorias (id, nombre, label)
VALUES (1, 'INFRAESTRUCTURA', 'Infraestructura'),
       (2, 'REDES', 'Redes'),
       (3, 'SOPORTE', 'Soporte Técnico'),
       (4, 'APLICACIONES', 'Gestión de Aplicaciones'),
       (5, 'SEGURIDAD', 'Seguridad de la Información');



create table servicios
(
    id          bigserial
        constraint servicios_pk
            primary key,
    tipo_id     bigint
        constraint servicios_tipos_id_fk
            references tipos,
    /*
categoria_id bigint
constraint servicios_categorias_id_fk
    references categorias,
    */
    nombre      text                 not null,
    descripcion text,
    activo      boolean default true not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);

INSERT INTO servicios (tipo_id, categoria_id, nombre)
VALUES (1, 1, 'Elaboración Especificaciones Técnicas para adquisición de equipos'), -- INFRAESTRUCTURA
       (1, 1, 'Verificación Especificaciones Técnicas para adquisición de equipos'),
       --REDES
       (1, 2, 'Acceso a red/internet'),
       -- SOPORTE
       (1, 2, 'Instalación de punto de acceso a red'),
       (1, 3, 'Diagnóstico de Hardware'),
       (1, 3, 'Instalación de Hardware o componentes'),
       (1, 3, 'Mantenimiento de Hardware'),

       (1, 3, 'Configuración de Impresora'),
       (1, 3, 'Configuración de Scanner'),
       (1, 3, 'Configuración de Proyector'),
       (1, 3, 'Configuración de otros dispositivos'),

       (1, 3, 'Instalación de Sistema Operativo'),
       (1, 3, 'Instalación de Aplicaciones'),
       (1, 3, 'Instalación de Antivirus'),

       (1, 3, 'Activación de Licencias'),

       (1, 4, 'Alta de acceso de usuario a Directorio Activo'),
       (1, 4, 'Baja de acceso de usuario a Directorio Activo'),

       (1, 3, 'Apoyo / acompañamiento de usuarios'),
       (1, 3, 'Capacitación de usuarios'),


       (1, 3, 'Visualización de Cámaras de Videovigilancia Internas'),


       -- APLICACIONES
       (1, 4, 'Alta de acceso de usuarios a Aplicaciones'),
       (1, 4, 'Baja de acceso de usuarios a Aplicaciones'),
       (1, 4, 'Modificación de permisos acceso de usuarios a Aplicaciones'),
       (1, 4, 'Reportes de Base de Datos'),
       (1, 4, 'Modificación de información en Base de Datos'),

       -- ///// INCIDENTES

       (2, 4, 'Usuario sin acceso a Internet'),
       (2, 4, 'Usuario sin acceso a Red'),
       (2, 4, 'Usuario sin acceso a Directorio Activo'),
       (2, 4, 'Usuario sin acceso a Aplicaciones'),

       -- ///// CAMBIOS
       (3, 4, 'Modificación de funcionalidades en Aplicaciones')
;



-- servicios - solicitudes ---- solicitudes x profesional
-- problemas - incidentes - categoria

Computadora
Internet
Impresora
Sistema institucional
Otro
--- usuario


/*


[ Reportar un problema ]
   computadora
   impresora
   internet
   sistema institucional

[ Solicitar un servicio ]
   acceso a sistema
   instalación de software
   crear usuario
   solicitar equipo

*/



create table estados
(
    id         bigserial
        constraint estados_pk
            primary key,
    nombre     varchar(200) not null,
    label      varchar(200) not null,
    orden      integer      not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);
INSERT INTO estados (id, nombre, label, orden)
VALUES (1, 'EN ESPERA', 'En espera', 1),
       (2, 'PROGRAMADO', 'Atención programada', 2),
       (3, 'ATENDIENDO', 'Atendiendo', 3),
       (4, 'RESUELTO', 'Resuelto', 4),
       (5, 'CERRADO', 'Cerrado', 5);

create table fases
(
    id         bigserial
        constraint fases_pk
            primary key,

    estado_id  bigint
        constraint fases_estados_id_fk
            references estados,

    nombre     varchar(200)          not null,
    label      varchar(200)          not null,
    orden      integer               not null,
    inicio     boolean default false not null,
    fin        boolean default false not null,
    finTec     boolean default false not null,
    alerta     boolean default false not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);

INSERT INTO fases (id, nombre, label, orden, estado_id, inicio, fin, finTec, alerta)
VALUES (1, 'NUEVO', 'Nuevo', 1, 1, true, false, false, true),
       (2, 'CATEGORIZADO', 'Categorizado', 2, 1, false, false, false, false),
       (3, 'ASIGNADO', 'Asignado', 3, 1, false, false, false, false),
       (4, 'PROGRAMADO', 'Programado', 4, 2, false, false, false, false),
       (5, 'ATENDIENDO', 'Atendiendo', 5, 3, false, false, false, false),
       (6, 'INFO_SOLICITADA', 'Información solicitada', 6, 3, false, false, false, true),
       (7, 'ATENDIDO', 'Atendido', 7, 4, false, false, true, true),
       (8, 'CERRADO', 'Cerrado', 8, 5, false, true, true, false);

-- NUEVO                EN_ESPERA
-- CATEGORIZADO         EN_ESPERA
-- ASIGNADO             EN_CURSO_ASIGNADO
-- PROGRAMADO           EN_CURSO_PROGRAMADO
-- ATENDIENDO           EN_CURSO_ATENDIENDO
-- INFO_SOLICITADA      EN_CURSO_INFO_SOLICITADA
-- RESUELTO             RESUELTO
-- CERRADO              CERRADO


create table acciones
(
    id                   bigserial
        constraint acciones_pk
            primary key,

    "faseEntrada"        bigint
        constraint acciones_fases_id_fk
            references fases,

    "faseSalida"         bigint
        constraint acciones_fases_id_fk_2
            references fases,

    nombre               varchar(200)          not null,
    label                varchar(200)          not null,
    "labelPasado"        varchar(200)          not null,
    "accionSolicitante"  boolean default false not null,
    "accionBeneficiario" boolean default false not null,
    "accionMesaServicio" boolean default false not null,
    "accionProfesional"  boolean default false not null,

    created_at           timestamp(0),
    updated_at           timestamp(0)
);

-- NULL                 -> NUEVO                  = CREAR                               -- usuario
-- NULL                 -> NUEVO                  = REGISTRAR                           -- mesa_servicio

--INSERT INTO acciones ("faseEntrada", "faseSalida", nombre, label, "labelPasado",
--                      "accionSolicitante", "accionBeneficiario", "accionMesaServicio", "accionProfesional")

VALUES (1, 2, 'CATEGORIZAR', 'Categorizar', 'Categorizado', false, false, true, false),
       -- NUEVO-1                -> CATEGORIZADO-2           = CATEGORIZAR                         -- mesa_servicio

       (2, 3, 'ASIGNAR', 'Asignar', 'Asignado', false, false, true, false),
       -- CATEGORIZADO-2         -> ASIGNADO-3               = ASIGNAR                             -- mesa_servicio

       (3, 5, 'ATENDER', 'Atender', 'Atendiendo', false, false, false, true),
       -- ASIGNADO-3             -> ATENDIENDO-5             = ATENDER                             -- profesional

       (3, 4, 'PROGRAMAR', 'Programar atención', 'Atención Programada', false, false, false, true),
       -- ASIGNADO-3             -> PLANIFICADO-4            = PLANIFICAR                          -- profesional

       (4, 5, 'ATENDER', 'Atender', 'Atendiendo', false, false, false, true),
       -- PLANIFICADO-4          -> ATENDIENDO-5             = ATENDER                             -- profesional

       (5, 6, 'SOLICITAR_INFO', 'Solicitar información', 'Información solicitada', false, false, false, true),
       -- ATENDIENDO-5           -> INFO_SOLICITADA-6         = SOLICITAR_INFO                      -- profesional

       (5, 5, 'REGISTRAR_VISITA_INFRUCTUOSA', 'Registrar visita infructuosa', 'Visita infructuosa', false, false,
        false, true),
       -- ATENDIENDO-5           -> ATENDIENDO-5             = REGISTRAR_VISITA_INFRUCTUOSA        -- profesional

       (5, 4, 'PROGRAMAR', 'Programar atención', 'Atención programada', false, false, false, true),
       -- ATENDIENDO-5           -> PLANIFICADO-4            = PLANIFICAR                          -- profesional

       (5, 7, 'REGISTRAR_RESOLUCION', 'Registrar atención realizar', 'Resuelto', false, false, false, true),
       -- ATENDIENDO-5           -> RESUELTO-7               = RESOLVER                            -- profesional

       (6, 5, 'ENVIAR_INFO', 'Enviar información', 'Información Agregada', true, true, false, false),
       -- INFO_SOLICITADA-6       -> ATENDIENDO-5             = ENVIAR_INFO                         -- usuario

       (7, 8, 'CONFIRMAR', 'Confirmar atención', 'Atención confirmada', true, true, false, false),
       -- RESUELTO-7             -> CERRADO-8                = CONFIRMAR                           -- usuario

       (3, 3, 'REASIGNAR', 'Reasignar', 'Reasignado', false, false, true, false),
       -- ASIGNADO-3             -> ASIGNADO-3               = REASIGNAR                          -- mesa_servicio

       (4, 4, 'REASIGNAR', 'Reasignar', 'Reasignado', false, false, true, false),
       -- PLANIFICADO-4          -> PLANIFICADO-4            = REASIGNAR                          -- mesa_servicio

       (5, 5, 'REASIGNAR', 'Reasignar', 'Reasignado', false, false, true, false),
       -- ATENDIENDO-5           -> ATENDIENDO-5             = REASIGNAR                          -- mesa_servicio

       (6, 6, 'REASIGNAR', 'Reasignar', 'Reasignado', false, false, true, false),
       -- INFO_SOLICITADA-6       -> INFO_SOLICITADA-6         = REASIGNAR                          -- mesa_servicio

       (1, 7, 'CANCELAR', 'Cancelar', 'Cancelado', true, true, true, false),
       -- NUEVO-1                -> RESUELTO-7               = CANCELAR                            -- usuario, mesa_servicio

       (2, 7, 'CANCELAR', 'Cancelar', 'Cancelado', true, true, true, false),
       -- CATEGORIZADO-2         -> RESUELTO-7               = CANCELAR                            -- usuario, mesa_servicio, profesional

       (5, 7, 'CANCELAR', 'Cancelar', 'Cancelado', true, true, true, true),
       -- ASIGNADO-3             -> RESUELTO-7               = CANCELAR                            -- usuario, mesa_servicio, profesional

       (5, 7, 'CANCELAR', 'Cancelar', 'Cancelado', true, true, true, true),
       -- PLANIFICADO-4          -> RESUELTO-7               = CANCELAR                            -- usuario, mesa_servicio, profesional

       (5, 7, 'CANCELAR', 'Cancelar', 'Cancelado', true, true, true, true),
       -- ATENDIENDO-5           -> RESUELTO-7               = CANCELAR                            -- usuario, mesa_servicio, profesional

       (5, 7, 'CANCELAR', 'Cancelar', 'Cancelado', true, true, true, true);
-- INFO_SOLICTADA-6       -> RESUELTO-7               = CANCELAR                            -- usuario, mesa_servicio, profesional


create table resultados
(
    id         bigserial
        constraint resultados_pk
            primary key,

    nombre     varchar(200) not null,
    label      varchar(200) not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);

INSERT INTO resultados (id, nombre, label)
VALUES (1, 'COMPLETAMENTE', 'Atendido completamente'),
       (2, 'PARCIALMENTE', 'Atendido parcialmente'),
       (3, 'NO_ATENDIDO', 'No atendido'),
       (4, 'CANCELADO', 'Cancelado');


create table fuentes
(
    id         bigserial
        constraint fuentes_pk
            primary key,
    nombre     varchar(200) not null,
    label      varchar(200) not null,
    orden      integer      not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);


INSERT INTO fuentes (id, nombre, label, orden)
VALUES (1, 'DIRECTA', 'Directa', 1),
       (2, 'APLICACION', 'Aplicación', 1),
       (3, 'TELEFONO', 'Teléfono', 2),
       (4, 'WHATSAPP', 'WhatsApp', 3),
       (5, 'EMAIL', 'Correo Electrónico', 4),
       (6, 'SGD', 'Sistema de Gestión Documental', 5),
       (7, 'OTRO', 'Otro', 6);

-- nro_visitas_infructuosas
create table tickets
(
    id                       bigserial
        constraint tickets_pk
            primary key,

    -- CREAR, REGISTRAR
    -- ------------------
    peticion                 varchar(250),                  -- title
    detalle                  text,                          -- body
    urgencia                 integer,
    "fechaCompleta"          timestamp(0),
    fecha                    date,
    hora                     time,
    "numDiaSemana"           varchar(50),
    "numHora"                varchar(50),
    ip                       varchar(15),

    fuente_id                bigint                not null
        constraint tickets_fuentes_id_fk
            references fuentes,

    "userSolicitante"        bigint                not null --- QUIEN SOLICITA
        constraint tickets_users_id_fk
            references users,

    "trabajadorSolicitante"  bigint                not null --- QUIEN SOLICITA
        constraint tickets_trabajadores_id_fk
            references trabajadores,

    "userBeneficiario"       bigint                not null -- QUIEN SE BENEFICIA
        constraint tickets_users_id_fk_2
            references users,

    "trabajadorBeneficiario" bigint                not null -- QUIEN SE BENEFICIA
        constraint tickets_trabajadores_id_fk_2
            references trabajadores,

    "porEncargo"             boolean default false not null,
    "contieneAdjuntos"       boolean default false not null,

    fase_id                  bigint                not null -- PLANIFICADO, ATENDIENDO, INFO_SOLICITADA, RESUELTO, CERRADO
        constraint tickets_fases_id_fk
            references fases,

    faseFecha                timestamp(0)          not null,

    -- CATEGORIZAR
    -- ------------------

    -- tickets_grande
    referencia_id            bigint
        constraint tickets_referencia_id_fk
            references servicios,

    -- tipo
    tipo_id                  bigint
        constraint tickets_tipos_id_fk
            references tipos,


    -- categoria
    categoria_id             bigint
        constraint tickets_categorias_id_fk
            references categorias,

    -- servicio
    servicio_id              bigint
        constraint tickets_servicios_id_fk
            references servicios,

    -- impacto
    impacto                  integer,

    -- prioridad, automatico
    prioridad                integer,


    -- ASIGNAR, REASIGANAR
    -- ------------------

    equipo_id                bigint
        constraint tickets_equipos_id_fk
            references equipos,

    userAsignado             bigint
        constraint tickets_users_id_fk_3
            references users,

    profesionalAsignado      bigint
        constraint tickets_profesionales_id_fk
            references profesiones,


    trabajadorAsignado       bigint
        constraint tickets_trabajadores_id_fk_3
            references trabajadores,

    --- RESULTADOS
    abierto                  boolean default true  not null,
    "paraCierre"             boolean default false not null,

    resultado_id             bigint
        constraint tickets_resultados_id_fk
            references resultados,

    "resultadoFecha"         timestamp(0),

    "resultadoDetalle"       text,

    created_at               timestamp(0),
    updated_at               timestamp(0)

);


create table archivos
(
    id         bigserial
        constraint archivos_pk
            primary key,
    path       varchar(200),
    name       varchar(200),
    size       varchar(200),
    sizeHuman  varchar(200),
    type       varchar(200),
    mime       varchar(200),
    created_at timestamp(0),
    updated_at timestamp(0)
);


create table movimientos
(
    id         bigserial
        constraint movimientos_pk
            primary key,

    ticket_id  bigint not null
        constraint movimientos_tickets_id_fk
            references tickets,

    accion_id  bigint not null
        constraint movimientos_acciones_id_fk
            references acciones,

    creado_por bigint not null
        constraint movimientos_users_id_fk
            references users,

    fecha      timestamp(0),

    created_at timestamp(0),
    updated_at timestamp(0)
);

create table adjuntos
(
    id            bigserial
        constraint adjuntos_pk
            primary key,

    ticket_id     bigint       not null
        constraint adjuntos_tickets_id_fk
            references movimientos,

    movimiento_id bigint       not null
        constraint adjuntos_movimientos_id_fk
            references movimientos,

    archivo_id    bigint       not null
        constraint adjuntos_archivos_id_fk
            references archivos,

    fecha         timestamp(0) not null,

    creado_por    bigint       not null
        constraint adjuntos_users_id_fk
            references users,

    orden         integer      not null,

    created_at    timestamp(0),
    updated_at    timestamp(0)
)



