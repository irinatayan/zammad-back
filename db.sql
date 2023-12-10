create table if not exists user
(
    id         int(11) unsigned auto_increment primary key,
    email      varchar(70)                         not null,
    username   varchar(70)                         not null,
    password   varchar(120)                        not null,
    role       varchar(20)                         not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
) charset = utf8mb4;

create table if not exists ticket_user
(
    id         int(11) unsigned auto_increment primary key,
    ticket_id  int(11) unsigned                    null,
    user_id    int(11) unsigned                    null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null,
    constraint user_id unique (user_id, ticket_id),
    constraint ticket_user_ibfk_1 foreign key (user_id) references user (id)
) charset = utf8mb4;


create table if not exists refresh_token
(
    token_hash VARCHAR(64)  not null,
    expires_at INT unsigned not null,
    primary key (token_hash),
    index (expires_at)
);