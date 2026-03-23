create table mesadeayuda.permissions
(
    id         bigserial
        primary key,
    name       varchar(255) not null,
    guard_name varchar(255) not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    constraint permissions_name_guard_name_unique
        unique (name, guard_name)
);

alter table mesadeayuda.permissions
    owner to csanchez;


create table mesadeayuda.model_has_permissions
(
    permission_id bigint       not null
        constraint model_has_permissions_permission_id_foreign
            references mesadeayuda.permissions
            on delete cascade,
    model_type    varchar(255) not null,
    model_id      bigint       not null,
    primary key (permission_id, model_id, model_type)
);

alter table mesadeayuda.model_has_permissions
    owner to csanchez;

create index model_has_permissions_model_id_model_type_index
    on mesadeayuda.model_has_permissions (model_id, model_type);

create table mesadeayuda.personal_access_tokens
(
    id             bigserial
        primary key,
    tokenable_type varchar(255) not null,
    tokenable_id   bigint       not null,
    name           text         not null,
    token          varchar(64)  not null
        constraint personal_access_tokens_token_unique
            unique,
    abilities      text,
    last_used_at   timestamp(0),
    expires_at     timestamp(0),
    created_at     timestamp(0),
    updated_at     timestamp(0)
);

alter table mesadeayuda.personal_access_tokens
    owner to csanchez;

create index personal_access_tokens_expires_at_index
    on mesadeayuda.personal_access_tokens (expires_at);

create index personal_access_tokens_tokenable_type_tokenable_id_index
    on mesadeayuda.personal_access_tokens (tokenable_type, tokenable_id);

create table mesadeayuda.roles
(
    id         bigserial
        primary key,
    name       varchar(255) not null,
    guard_name varchar(255) not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    constraint roles_name_guard_name_unique
        unique (name, guard_name)
);

alter table mesadeayuda.roles
    owner to csanchez;

create table mesadeayuda.model_has_roles
(
    role_id    bigint       not null
        constraint model_has_roles_role_id_foreign
            references mesadeayuda.roles
            on delete cascade,
    model_type varchar(255) not null,
    model_id   bigint       not null,
    primary key (role_id, model_id, model_type)
);

alter table mesadeayuda.model_has_roles
    owner to csanchez;

create index model_has_roles_model_id_model_type_index
    on mesadeayuda.model_has_roles (model_id, model_type);

create table mesadeayuda.role_has_permissions
(
    permission_id bigint not null
        constraint role_has_permissions_permission_id_foreign
            references mesadeayuda.permissions
            on delete cascade,
    role_id       bigint not null
        constraint role_has_permissions_role_id_foreign
            references mesadeayuda.roles
            on delete cascade,
    primary key (permission_id, role_id)
);

alter table mesadeayuda.role_has_permissions
    owner to csanchez;

create table mesadeayuda.sessions
(
    id            varchar(255) not null
        primary key,
    user_id       bigint,
    ip_address    varchar(45),
    user_agent    text,
    payload       text         not null,
    last_activity integer      not null
);

alter table mesadeayuda.sessions
    owner to csanchez;

create index sessions_last_activity_index
    on mesadeayuda.sessions (last_activity);

create index sessions_user_id_index
    on mesadeayuda.sessions (user_id);

create table mesadeayuda.users
(
    id         bigserial
        primary key,
    usuario    varchar(30)          not null
        constraint users_pk
            unique,
    dni        varchar(10)          not null
        constraint users_pk_2
            unique,
    paterno    varchar(100)         not null,
    materno    varchar(100)         not null,
    nombres    varchar(100)         not null,
    activo     boolean default true not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table mesadeayuda.users
    owner to csanchez;

