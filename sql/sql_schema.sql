create table countries
(
    id   int auto_increment
        primary key,
    name varchar(255) default '' not null
)
    charset = utf8;

create table demodata
(
    id         int auto_increment
        primary key,
    state      varchar(255) null,
    genderName text         null,
    gender     int          null,
    age        varchar(255) null,
    raceName   text         null,
    race       int          null,
    pop        int          null
);

create table fundRequests
(
    id         int auto_increment
        primary key,
    party      int         default 0  not null,
    requester  int         default 0  not null,
    requesting double      default 0  not null,
    reason     varchar(50) default '' not null,
    fulfilled  int         default 0  null,
    secret     varchar(50)            null
);

create table parties
(
    id            int auto_increment
        primary key,
    partyBio      varchar(1000)           null,
    partyPic      varchar(900)            null,
    nation        varchar(50)             null,
    name          varchar(60)             null,
    initialEcoPos double                  null,
    initialSocPos double                  null,
    ecoPos        double                  null,
    socPos        double                  null,
    partyRoles    mediumtext              null,
    discord       varchar(16) default '0' null,
    partyTreasury double      default 0   null
)
    comment 'table for political parties' charset = utf8;

create table states
(
    id           int auto_increment
        primary key,
    name         varchar(255)  not null,
    abbreviation varchar(255)  not null,
    active       int default 0 null,
    country      varchar(255)  null,
    flag         text          null
)
    charset = utf8;

create table users
(
    id              int auto_increment
        primary key,
    admin           int           default 0                             null,
    username        text                                                not null,
    password        text                                                not null,
    regCookie       varchar(255)                                        not null,
    currentCookie   varchar(255)                                        not null,
    regIP           text                                                not null,
    currentIP       text                                                not null,
    hsi             double        default 10                            not null,
    politicianName  varchar(55) collate utf8_unicode_ci                 not null,
    lastOnline      varchar(500)  default '0'                           not null,
    profilePic      varchar(2500) default 'images/userPics/default.jpg' null,
    bio             varchar(2500) default 'I am gay!'                   not null,
    state           varchar(255)                                        not null,
    nation          varchar(255)                                        not null,
    ecoPos          double        default 0                             not null,
    socPos          double        default 0                             not null,
    authority       double        default 50                            null,
    campaignFinance bigint        default 50000                         null,
    party           int           default 0                             null,
    partyInfluence  double        default 0                             null,
    partyVotingFor  int           default 0                             null
)
    charset = utf8;

