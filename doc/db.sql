USE `chinapub`;

-- 首页推荐
-- DROP TABLE IF EXISTS `home_page_book`;
CREATE TABLE `home_page_book` (
    `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
    `book_id` int(20) NOT NULL COMMENT '图书id',
    `create_at` int NOT NULL COMMENT '纪录创建时间',
    `update_at` int NOT NULL COMMENT '纪录最后更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页推荐';

-- 书籍信息
-- DROP TABLE IF EXISTS `book`;
CREATE TABLE `book` (
    `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
    `book_id` int(20) NOT NULL COMMENT '图书id',

    `from_id` int(20) NOT NULL COMMENT '来源',
    `title` varchar(256) NOT NULL DEFAULT '' COMMENT '图书标题',
    `book_url` varchar(1024) NOT NULL COMMENT '图书链接',
    `cover_url` varchar(2048) NOT NULL DEFAULT '' COMMENT '封面图片链接',
    `category` varchar(2048) NOT NULL DEFAULT '' COMMENT '分类',
    `author` varchar(512) NOT NULL DEFAULT '' COMMENT '作者',
    `press` varchar(512) NOT NULL DEFAULT '' COMMENT '出版社',
    `ISBN` varchar(128) NOT NULL DEFAULT '' COMMENT 'ISBN',
    `process_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '处理状态 0 未处理 1 已处理',

    `create_at` int(11) NOT NULL COMMENT '纪录创建时间',
    `update_at` int(11) NOT NULL COMMENT '纪录最后更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_from` (`from_id`),
    UNIQUE KEY `idx_book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图书信息';

-- 推荐信息
-- DROP TABLE IF EXISTS `rec_book`;
CREATE TABLE `rec_book` (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `book_id_from` int(20) NOT NULL COMMENT '图书id',
  `book_id_to` int(20) NOT NULL COMMENT '图书id',
  `create_at` int(11) NOT NULL COMMENT '纪录创建时间',
  `update_at` int(11) NOT NULL COMMENT '纪录最后更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_from_to` (`book_id_from`, `book_id_to`),
  KEY `idx_to` (`book_id_to`)
) ENGINE=InnoDB CHARSET=utf8 COMMENT='图书推荐关系';
