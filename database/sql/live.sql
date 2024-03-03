CREATE TABLE `live_team`(
    `id` tinyint(1) unsigned not null auto_increment,
    `name` varchar(20) not null default '',
    `image` varchar(20) not null default '',
    `type` tinyint(1) unsigned not null default 0 comment '球队分区 1:东 2:西',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '球队表';

CREATE TABLE `live_game`(
    `id` int(10) unsigned not null auto_increment,
    `home_id` tinyint(1) unsigned not null default 0 comment '主队id',
    `away_id` tinyint(1) unsigned not null default 0 comment '客队id',
    `home_score` int(10) unsigned not null default 0 comment '主队比分',
    `away_score` int(10) unsigned not null default 0 comment '客队比分',
    `narrator` varchar(20) not null default '' comment '直播员',
    `image` varchar(20) not null default '',
    `start_time` int(10) unsigned not null default 0,
    `status` tinyint(1) unsigned not null default 0 comment '状态',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '直播表';

CREATE TABLE `live_player`(
    `id` int(10) unsigned not null auto_increment,
    `name` varchar(20) not null default '',
    `image` varchar(50) not null default '',
    `age` tinyint(1) unsigned not null default 0,
    `position` tinyint(1) unsigned not null default 0,
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '球员表';

CREATE TABLE `live_outs`(
    `id` int(10) unsigned not null auto_increment,
    `game_id` int(10) unsigned not null default 0,
    `team_id` tinyint(1) unsigned not null default 0,
    `content` varchar(200) not null default '',
    `image` varchar(50) not null default '',
    `type` tinyint(1) unsigned not null default 0 comment '比赛处于第几节',
    `status` tinyint(1) unsigned not null default 0 comment '状态',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '赛事赛况表';

CREATE TABLE `live_chart`(
    `id` int(10) unsigned not null auto_increment,
    `game_id` int(10) unsigned not null default 0,
    `user_id` tinyint(1) unsigned not null default 0,
    `content` varchar(200) not null default '',
    `image` varchar(20) not null default '',
    `status` tinyint(1) unsigned not null default 0 comment '状态',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '聊天室表';

CREATE TABLE `live_stat`(
    `id` int(10) unsigned not null auto_increment,
    `game_id` int(10) unsigned not null default 0,
    `home_id` tinyint(1) unsigned not null default 0 comment '主队id',
    `away_id` tinyint(1) unsigned not null default 0 comment '客队id',
    `image` varchar(20) not null default '',
    `status` tinyint(1) unsigned not null default 0 comment '状态',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '数据表';

CREATE TABLE `live_outs_stat`(
    `id` int(10) unsigned not null auto_increment,
    `game_id` int(10) unsigned not null default 0,
    `team_id` tinyint(1) unsigned not null default 0 comment '主队id',
    `section_one` tinyint(1) unsigned not null default 0 comment '第一节',
    `section_two` tinyint(1) unsigned not null default 0 comment '第二节',
    `section_three` tinyint(1) unsigned not null default 0 comment '第三节',
    `section_four` tinyint(1) unsigned not null default 0 comment '第四节',
    `total` tinyint(2) unsigned not null default 0 comment '总分',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '赛况数据表';

CREATE TABLE `live_user`(
    `id` tinyint(1) unsigned not null auto_increment,
    `phone` varchar(20) not null default '',
    `created_at` varchar(20) not null default 0,
    `updated_at` varchar(20) not null default 0,
    primary key(`id`)
) ENGINE = InnoDB auto_increment = 1 DEFAULT charset = utf8 comment '用户表';