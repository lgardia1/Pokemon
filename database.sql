create database pokemondatabase
    default character set utf8
    collate utf8_unicode_ci;

use pokemondatabase;

create table pokemon (
  id bigint(20) not null auto_increment primary key,
  name varchar(100) not null unique,
  weight decimal(9,2) not null,
  height decimal(9,2) not null,
  evolution integer,
  type ENUM('water', 'ground', 'rock', 'fire', 'grass', 'electric', 'psychic', 'ice', 'dragon', 'dark', 'fairy') not null
) engine=innodb default charset=utf8 collate=utf8_unicode_ci;

create user pokemonuser@localhost
    identified by 'pokemonpassword';

grant all
    on pokemondatabase.*
    to pokemonuser@localhost;

flush privileges;