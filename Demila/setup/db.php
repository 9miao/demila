<?php
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------


function sql_quote($value, $toStrip = true) {
	$value = str_replace('<x>', '', $value);
	if(get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	$value = addslashes($value);

	return $value;
}


	mysql_query("
		ALTER DATABASE `" . $_POST['mysql_db'] . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `attributes` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `category_id` int(11) NOT NULL COMMENT '所属属性类别的ID',
		  `name` varchar(255) NOT NULL COMMENT '属性名称',
		  `photo` varchar(255) NOT NULL COMMENT '图片',
		  `visible` enum('true','false') NOT NULL default 'false' COMMENT '可见',
		  `order_index` int(11) NOT NULL default '0' COMMENT '排序',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 COMMENT='属性';
	");

        mysql_query("
            CREATE TABLE IF NOT EXISTS `app_extends` (
		    `id` int(11) NOT NULL AUTO_INCREMENT,
            `extend_name` VARCHAR (50) NOT NULL COMMENT '扩展应用名称',
            `state` tinyint(1)  DEFAULT '1'  COMMENT '开启状态（1：打开，0：未打开）',
            `m` VARCHAR (50) NOT NULL COMMENT '模块名称',
            `c` VARCHAR (50) NOT NULL COMMENT '动作名称',
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='扩展应用表';
        ");


	mysql_query("
		CREATE TABLE IF NOT EXISTS `attributes_categories` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(255) NOT NULL COMMENT '属性类别名称',
          `type` enum('select','multiple','check','radio','input') COLLATE utf8_unicode_ci NOT NULL COMMENT '类型',
		  `categories` TEXT NOT NULL COMMENT '启用该属性类别的作品类别',
		  `visible` enum('true','false') NOT NULL default 'false' COMMENT '可见',
		  `order_index` int(11) NOT NULL COMMENT '排序',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1  COMMENT='属性类别';
	");

	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `bulletin` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(255) NOT NULL COMMENT '广播名称',
		  `text` longtext NOT NULL COMMENT '广播内容',
		  `datetime` datetime NOT NULL COMMENT '发送时间',
		  `send_to` varchar(20) NOT NULL COMMENT '发送范围',
		  `send_id` int(11) NOT NULL COMMENT '发送者ID',
		  `readed` int(11) NOT NULL default '0' COMMENT '阅读次数',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM COMMENT='广播';
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `bulletin_emails` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `subname` varchar(255) NOT NULL COMMENT '订阅者名称',
		  `email` varchar(255) NOT NULL COMMENT '电子邮件地址',
		  `bulletin_subscribe` enum('true','false') NOT NULL default 'true' COMMENT '订阅确认',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM COMMENT='广播邮件列表';
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `bulletin_template` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `template` longtext NOT NULL COMMENT '广播模板',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  COMMENT='广播模板';
	");

	mysql_query('
		INSERT INTO `bulletin_template` (`id`, `template`) VALUES
		(1, \'<html>\r\n<head>\r\n</head>\r\n<body>\r\n<img src="http://{$DOMAIN}/bulletin/read/?bulletin_id={$BULLETINID}" alt="" />\r\n<br /><br />\r\n\r\n{$CONTENT}\r\n\r\n<br /><br />\r\n<a href="http://{$DOMAIN}/bulletin/delete/?email={$EMAIL}">退订</a>\r\n</body>\r\n</html>\');
	');

	mysql_query("
		CREATE TABLE IF NOT EXISTS `categories` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `sub_of` int(11) NOT NULL COMMENT '所属类别',
		  `meta_title` varchar(255) NOT NULL COMMENT '元标题',
		  `meta_keywords` varchar(255) NOT NULL COMMENT '元关键词',
		  `meta_description` text NOT NULL COMMENT '元描述',
		  `name` varchar(255) NOT NULL COMMENT '类别名称',
		  `text` longtext NOT NULL COMMENT '描述',
		  `visible` enum('true','false') NOT NULL default 'false' COMMENT '可见',
		  `order_index` int(11) NOT NULL COMMENT '排序',
		  PRIMARY KEY  (`id`),
		  KEY `category_id` (`sub_of`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1  COMMENT='作品类别';
	");

	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `collections` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '创建者ID',
		  `name` varchar(255) NOT NULL COMMENT '收藏夹名称',
		  `text` text NOT NULL COMMENT '描述',
		  `photo` varchar(255) NOT NULL COMMENT '图片',
		  `items` int(11) NOT NULL default '0' COMMENT '作品数',
		  `rating` int(11) NOT NULL default '0' COMMENT '平均星数',
		  `votes` int(11) NOT NULL default '0' COMMENT '总投票数',
		  `score` int(11) NOT NULL default '0' COMMENT '总星数',
		  `datetime` datetime NOT NULL COMMENT '创建时间',
		  `public` enum('true','false') NOT NULL default 'false' COMMENT '公开显示',
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=MyISAM COMMENT='收藏夹';
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `collections_rates` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `collection_id` int(11) NOT NULL COMMENT '收藏夹ID',
		  `user_id` int(11) NOT NULL COMMENT '创建者ID',
		  `rate` int(11) NOT NULL COMMENT '创建者评价的星数',
		  `datetime` datetime NOT NULL COMMENT '评星时间',
		  PRIMARY KEY  (`id`),
		  KEY `collection_id` (`collection_id`,`user_id`)
		) ENGINE=MyISAM COMMENT='收藏夹评星记录';
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `contacts` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(255) NOT NULL COMMENT '发起者名称',
		  `email` varchar(255) NOT NULL COMMENT '发起者电子邮件',
		  `issue_id` int(11) NOT NULL default '0' COMMENT '事项所属类别ID',
		  `issue` varchar(255) NOT NULL COMMENT '事项所属类别名称',
		  `short_text` longtext NOT NULL COMMENT '内容摘要',
		  `datetime` datetime NOT NULL COMMENT '发起时间',
		  `answer` longtext NOT NULL COMMENT '回复内容',
		  `answer_datetime` datetime NOT NULL COMMENT '回复时间',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB COMMENT='联系我们';
	");
	

	mysql_query("
		CREATE TABLE IF NOT EXISTS `contacts_categories` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
	  `name` varchar(255) NOT NULL COMMENT '类别名称',
	  `text` longtext NOT NULL COMMENT '描述',
	  `visible` enum('true','false') NOT NULL default 'false' COMMENT '可见',
	  `order_index` int(11) NOT NULL default '0' COMMENT '排序',
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM COMMENT='联系我们类别';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `countries` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(255) NOT NULL COMMENT '国家国地区的名称',
		  `photo` varchar(255) NOT NULL COMMENT '国旗或旗帜',
		  `visible` enum('true','false') NOT NULL default 'false' COMMENT '可见',
		  `order_index` int(11) NOT NULL default '0' COMMENT '排序',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB COMMENT='国家或地区';
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `currency` (
		  `code` varchar(3) NOT NULL COMMENT '货币代码',
		  `name` varchar(100) NOT NULL COMMENT '货币名称',
		  `symbol` varchar(50) NOT NULL COMMENT '货币符号',
		  `active` enum('yes','no') NOT NULL default 'no' COMMENT '启用',
		  PRIMARY KEY  (`code`)
		) ENGINE=MyISAM COMMENT='货币';
	");
	
	mysql_query("
		INSERT INTO `currency` (`code`, `name`, `symbol`, `active`) VALUES
			('EUR', 'Euro', '&euro;', 'no'),
			('CNY', 'CNY', '￥', 'yes'),
			('USD', 'U.S. Dollar ', '$', 'no');
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `deposit` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '到账者ID',
		  `deposit` float NOT NULL COMMENT '充值金额',
		  `paid` enum('true','false') NOT NULL default 'false' COMMENT '到账',
		  `datetime` datetime NOT NULL COMMENT '充值时间',
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1  COMMENT='充值';
	");
	
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `history` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '到账者ID',
		  `action` varchar(255) NOT NULL COMMENT '动作',
		  `transaction_id` varchar(255) NOT NULL COMMENT '交易ID',
		  `datetime` datetime NOT NULL COMMENT '发生时间',
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1  COMMENT='充值记录';
	");
	
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '上传者ID',
		  `name` varchar(50) NOT NULL COMMENT '作品名称',
		  `description` text NOT NULL COMMENT '详细描述',
		  `thumbnail` varchar(255) NOT NULL COMMENT '缩略图',
		  `theme_preview` varchar(255) NOT NULL COMMENT '预览文件包',
		  `main_file` varchar(255) NOT NULL COMMENT '主文件包',
		  `main_file_name` varchar(255) NOT NULL COMMENT '主文件包全称',
		  `categories` varchar(100) NOT NULL COMMENT '所属类别',
		  `demo_url` varchar(255) NOT NULL COMMENT '演示地址',
		  `price` float NOT NULL default '0' COMMENT '定价',
		  `sales` int(11) NOT NULL default '0' COMMENT '销售数量',
		  `earning` float NOT NULL default '0' COMMENT '总销售额',
		  `rating` int(11) NOT NULL default '0' COMMENT '平均星数',
		  `votes` int(11) NOT NULL default '0' COMMENT '总投票数',
		  `score` int(11) NOT NULL default '0' COMMENT '总星数',
		  `comments` int(11) NOT NULL default '0' COMMENT '评论数',
		  `free_request` enum('true','false') NOT NULL default 'false' COMMENT '请求免费上架',
		  `free_file` enum('true','false') NOT NULL default 'false' COMMENT '当前免费文件',
		  `weekly_to` date default NULL COMMENT '周推荐至',
		  `reviewer_comment` text NOT NULL COMMENT '给审核员的说明',
		  `datetime` datetime NOT NULL COMMENT '上传时间',
		  `status` enum('active','queue','unapproved','extended_buy','deleted') NOT NULL default 'queue' COMMENT '状态',
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`user_id`,`categories`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1  COMMENT='作品';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_attributes` (
		  `item_id` int(11) NOT NULL COMMENT '作品ID',
		  `attribute_id` VARCHAR(255) NOT NULL COMMENT '属性ID',
		  `category_id` int(11) NOT NULL COMMENT '属性类别的ID',
		  KEY `item_id` (`item_id`,`attribute_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='作品属性' ;
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_collections` (
		  `item_id` int(11) NOT NULL COMMENT '作品ID',
		  `collection_id` int(11) NOT NULL COMMENT '收藏夹ID',
		  PRIMARY KEY  (`item_id`,`collection_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='作品收藏夹';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_comments` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
	  `owner_id` int(11) NOT NULL COMMENT '作品作者ID',
	  `item_id` int(11) NOT NULL COMMENT '作品ID',
	  `item_name` varchar(255) NOT NULL COMMENT '作品名称',
	  `user_id` int(11) NOT NULL COMMENT '评论者ID',
	  `comment` text NOT NULL COMMENT '评论内容',
	  `datetime` datetime NOT NULL COMMENT '评论时间',
	  `notify` enum('true','false') NOT NULL default 'false' COMMENT '回复提醒',
	  `reply_to` int(11) NOT NULL default '0' COMMENT '回复所属评论的ID',
	  `report_by` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`id`),
	  KEY `item_id` (`item_id`,`user_id`),
	  KEY `owner_id` (`owner_id`),
	  KEY `report_by` (`report_by`)
	) ENGINE=MyISAM COMMENT='作品评论';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_faqs` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `item_id` int(11) NOT NULL COMMENT '所属作品ID',
		  `user_id` int(11) NOT NULL COMMENT '创建者ID',
		  `question` text NOT NULL COMMENT '问题内容',
		  `answer` text NOT NULL COMMENT '解答内容',
		  `datetime` datetime NOT NULL COMMENT '创建时间',
		  PRIMARY KEY  (`id`),
		  KEY `item_id` (`item_id`,`user_id`)
		) ENGINE=MyISAM COMMENT='作品常见问题与解答';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_rates` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `item_id` int(11) NOT NULL COMMENT '作品ID',
		  `user_id` int(11) NOT NULL COMMENT '购买者ID',
		  `rate` int(11) NOT NULL COMMENT '购买者评价的星数',
		  `datetime` datetime NOT NULL COMMENT '评星时间',
		  PRIMARY KEY  (`id`),
		  KEY `collection_id` (`item_id`,`user_id`)
		) ENGINE=MyISAM COMMENT='作品评星记录';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_tags` (
		  `item_id` int(11) NOT NULL COMMENT '原作品ID',
		  `tag_id` int(11) NOT NULL COMMENT '新增标签ID',
		  `type` enum('usage','style','features') NOT NULL COMMENT '新增标签类型',
		  PRIMARY KEY  (`item_id`,`tag_id`,`type`),
		  KEY `tag_id` (`tag_id`),
		  KEY `item_id` (`item_id`)
		) ENGINE=MyISAM COMMENT='处于更新队列的作品新增的标签';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `orders` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '订单创建者ID',
		  `owner_id` int(11) NOT NULL COMMENT '订单中作品所属作者的ID',
		  `item_id` int(11) NOT NULL COMMENT '作品ID',
		  `item_name` varchar(255) NOT NULL COMMENT '作品名称',
		  `price` float NOT NULL COMMENT '订单价格',
		  `receive` float NOT NULL default '0' COMMENT '作品所属作者获得的分成',
		  `datetime` datetime NOT NULL COMMENT '订单创建时间',
		  `paid` enum('true','false') NOT NULL default 'false' COMMENT '到账',
		  `paid_datetime` datetime NOT NULL COMMENT '到账时间',
		  `extended` enum('true','false') NOT NULL default 'false',
		  `type` enum('buy','referal') NOT NULL default 'buy' COMMENT '订单类型',
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`user_id`,`item_id`),
		  KEY `owner_id` (`owner_id`)
		) ENGINE=MyISAM COMMENT='订单';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `pages` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `sub_of` int(11) NOT NULL COMMENT '父页面ID',
		  `key` varchar(255) NOT NULL COMMENT 'URL地址关键词',
		  `meta_title` varchar(255) NOT NULL COMMENT '元标题',
		  `meta_keywords` varchar(255) NOT NULL COMMENT '元关键词',
		  `meta_description` text NOT NULL COMMENT '元描述',
		  `name` varchar(255) NOT NULL COMMENT '页面名称',
		  `text` longtext NOT NULL COMMENT '页面详细内容',
		  `menu` enum('true','false') NOT NULL default 'false' COMMENT '加入顶部菜单',
		  `footer` ENUM( 'true', 'false' ) NOT NULL,
		  `visible` enum('true','false') NOT NULL default 'false' COMMENT '可见',
		  `order_index` int(11) NOT NULL COMMENT '排序',
		  PRIMARY KEY  (`id`),
		  KEY `category_id` (`sub_of`),
		  KEY `key` (`key`)
		) ENGINE=InnoDB COMMENT='自定义页面';
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `percents` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `percent` int(11) NOT NULL COMMENT '百分比',
		  `from` int(11) NOT NULL COMMENT '从…(某金额)',
		  `to` int(11) NOT NULL COMMENT '到…(某金额)',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM COMMENT='分成比例';
	");
	
	mysql_query("
		INSERT INTO `percents` (`id`, `percent`, `from`, `to`) VALUES
			(1, 60, 0, 5000);
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `quiz` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(255) NOT NULL COMMENT '问题内容',
		  `order_index` int(11) NOT NULL COMMENT '排序',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  COMMENT='测验问题';
	");
	
	mysql_query("
        INSERT INTO `quiz` (`id`, `name`, `order_index`) VALUES
         (1, '5 + 5 = ?', 4),
         (2, '10 - 7 = ?', 1),
         (3, '2 * 2 = ?', 2),
         (4, '15 : 3 = ?', 3),
         (5, '10 * 10 = ?', 5);
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `quiz_answers` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `quiz_id` int(11) NOT NULL COMMENT '测验问题ID',
		  `name` varchar(255) NOT NULL COMMENT '答案内容',
		  `right` enum('true','false') NOT NULL default 'false' COMMENT '正确答案',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  COMMENT='测验问题的答案';
	");
	
	mysql_query("
        INSERT INTO `quiz_answers` (`id`, `quiz_id`, `name`, `right`) VALUES
         (1, 1, '11', 'false'),
         (2, 1, '8', 'false'),
         (3, 1, '10', 'true'),
         (4, 1, '5', 'false'),
         (5, 2, '7', 'false'),
         (6, 2, '3', 'true'),
         (7, 2, '8', 'false'),
         (8, 2, '4', 'false'),
         (9, 3, '5', 'false'),
         (10, 3, '1', 'false'),
         (11, 3, '6', 'false'),
         (12, 3, '4', 'true'),
         (13, 4, '5', 'true'),
         (14, 4, '4', 'false'),
         (15, 4, '6', 'false'),
         (16, 4, '7', 'false'),
         (17, 5, '200', 'false'),
         (18, 5, '100', 'true'),
         (19, 5, '20', 'false'),
         (20, 5, '10', 'false');
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `system` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `key` varchar(255) NOT NULL COMMENT '系统关键词',
		  `value` text NOT NULL COMMENT '具体的值',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  COMMENT='系统设置';
	");
	
	mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`) VALUES
			(1, 'meta_title', '".sql_quote($_POST['meta_title'])."'),
			(2, 'meta_keywords', '".sql_quote($_POST['meta_keywords'])."'),
			(3, 'meta_description', '".sql_quote($_POST['meta_description'])."'),
			(4, 'admin_mail', '".sql_quote($_POST['admin_mail'])."'),
			(5, 'report_mail', '".sql_quote($_POST['report_mail'])."');
	");

	mysql_query("
		CREATE TABLE IF NOT EXISTS `tags` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(255) NOT NULL COMMENT '标签内容',
		  PRIMARY KEY  (`id`),
		  KEY `id` (`id`)
		) ENGINE=MyISAM COMMENT='标签';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `temp_items` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `item_id` int(11) NOT NULL COMMENT '原作品ID',
		  `name` varchar(255) NOT NULL COMMENT '原作品名称',
		  `thumbnail` varchar(255) NOT NULL COMMENT '更新的缩略图',
		  `theme_preview` varchar(255) NOT NULL COMMENT '更新的预览文件包',
		  `main_file` varchar(255) NOT NULL COMMENT '更新的主文件包',
		  `main_file_name` varchar(255) NOT NULL COMMENT '更新的主文件包全称',
		  `reviewer_comment` text NOT NULL COMMENT '给更新审核员的说明',
		  `datetime` datetime NOT NULL COMMENT '提交更新时间',
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`item_id`)
		) ENGINE=MyISAM COMMENT='处于更新队列的作品';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `temp_items_tags` (
		  `item_id` int(11) NOT NULL COMMENT '原作品ID',
		  `tag_id` int(11) NOT NULL COMMENT '新增标签ID',
		  `type` enum('usage','style','features') NOT NULL COMMENT '新增标签类型',
		  PRIMARY KEY  (`item_id`,`tag_id`,`type`),
		  KEY `tag_id` (`tag_id`),
		  KEY `item_id` (`item_id`)
		) ENGINE=MyISAM COMMENT='处于更新队列的作品新增的标签';
	");
	
	mysql_query("
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '电子邮件地址',
  `nickname` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '昵称',
  `featured_item_id` int(11) NOT NULL DEFAULT '0' COMMENT '自荐作品ID',
  `exclusive_author` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false' COMMENT '独家作者',
  `license` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a:1:{s:8:\"personal\";s:8:\"personal\";}' COMMENT '作品许可',
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '头像',
  `homeimage` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '个人主页图片',
  `firmname` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '公司名称',
  `profile_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '个人资料标题',
  `profile_desc` text COLLATE utf8_unicode_ci NOT NULL COMMENT '个人资料详情',
  `live_city` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '所在城市',
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '详细地址',
  `country_id` int(11) NOT NULL DEFAULT '0' COMMENT '国家或地区的ID',
  `custom_made` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false' COMMENT '承接定制',
  `social` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quiz` enum('false','true') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false' COMMENT '通过测验问题',
  `deposit` float NOT NULL DEFAULT '0' COMMENT '充值金额的余额',
  `earning` float NOT NULL DEFAULT '0' COMMENT '分成所得金额的余额',
  `total` float NOT NULL DEFAULT '0' COMMENT '总余额',
  `sold` float NOT NULL DEFAULT '0' COMMENT '售出作品总值',
  `items` int(11) NOT NULL DEFAULT '0' COMMENT '在售作品数量',
  `sales` int(11) NOT NULL DEFAULT '0' COMMENT '作品销售次数',
  `buy` int(11) NOT NULL DEFAULT '0' COMMENT '购买作品的数量',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT '平均星数',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '总星数',
  `votes` int(11) NOT NULL DEFAULT '0' COMMENT '总投票数',
  `referals` int(11) NOT NULL DEFAULT '0' COMMENT '推广的用户数量',
  `referal_money` float NOT NULL DEFAULT '0' COMMENT '推广的总金额',
  `featured_author` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false' COMMENT '推荐用户',
  `power_elite_author` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false' COMMENT '强力精英作者',
  `elite_author` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false' COMMENT '精英作者',
  `register_datetime` datetime DEFAULT NULL COMMENT '注册时间',
  `last_login_datetime` datetime DEFAULT NULL COMMENT '最后登录时间',
  `ip_address` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '最后登录IP地址',
  `status` enum('waiting','banned','activate') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'waiting' COMMENT '用户状态',
  `groups` text COLLATE utf8_unicode_ci COMMENT '所属权限组',
  `remember_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activate_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '激活码',
  `referal_id` int(11) NOT NULL DEFAULT '0' COMMENT '推广者ID',
  `commission_percent` int(2) NOT NULL DEFAULT '0' COMMENT '单独分成比例',
  `badges` text COLLATE utf8_unicode_ci NOT NULL COMMENT '管理员授予的徽章',
  `weibo` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '新浪微博URL地址',
  `tencent` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '腾讯微博URL地址',
  `baidu` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '百度空间URL地址',
  `netease` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '网易微博URL地址',
  `sohu` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '搜狐微博URL地址',
  `renren` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '人人空间URL地址',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `featured_item_id` (`featured_item_id`),
  KEY `referal_id` (`referal_id`)
		) ENGINE=InnoDB  COMMENT='用户';
	");
	
	mysql_query("
		INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `nickname`, `featured_item_id`, `exclusive_author`, `license`, `avatar`, `homeimage`, `firmname`, `profile_title`, `profile_desc`, `live_city`, `country_id`, `custom_made`, `social`, `quiz`, `deposit`, `earning`, `total`, `sold`, `items`, `sales`, `buy`, `rating`, `score`, `votes`, `referals`, `referal_money`, `featured_author`, `power_elite_author`, `elite_author`, `register_datetime`, `last_login_datetime`, `ip_address`, `status`, `groups`, `remember_key`, `activate_key`, `referal_id`, `commission_percent`, `badges`, `weibo`, `tencent`, `baidu`, `netease`, `sohu`, `renren`) VALUES
		(1, '".sql_quote($_POST['admin_username'])."', '".md5(md5($adminPassword))."', '".sql_quote($_POST['admin_mail'])."', 'admin', 0, 'false', 'a:2:{s:8:\"extended\";s:8:\"extended\";s:8:\"personal\";s:8:\"personal\";}', '', '', '', '', '', '', 1, 'true', '', 'true', 500, 0, 500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'false', 'true', 'false', '2013-06-06 00:00:00', '2013-06-06 00:00:00', '127.0.0.1', 'activate', 'a:1:{i:2;s:2:\"on\";}', '', NULL, 0, 0, '45,47', '', '', '', '', '', '');
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `users_emails` (
		  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
		  `from_id` int(11) NOT NULL COMMENT '发件者用户ID',
		  `from_email` varchar(255) NOT NULL COMMENT '发件者电子邮件地址',
		  `to_id` int(11) NOT NULL COMMENT '收件者ID',
		  `message` text NOT NULL COMMENT '邮件内容',
		  `datetime` datetime NOT NULL COMMENT '发送时间',
		  PRIMARY KEY  (`id`),
		  KEY `from_id` (`from_id`,`to_id`)
		) ENGINE=MyISAM  COMMENT='用户联系邮件';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `users_followers` (
		  `user_id` int(11) NOT NULL COMMENT '被关注者ID',
		  `follow_id` int(11) NOT NULL COMMENT '粉丝ID',
		  PRIMARY KEY  (`user_id`,`follow_id`)
		) ENGINE=MyISAM  COMMENT='用户粉丝';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `user_groups` (
		  `ug_id` int(11) NOT NULL auto_increment COMMENT '用户组ID',
		  `name` varchar(30) NOT NULL COMMENT '用户组名称',
		  `description` varchar(255) NOT NULL COMMENT '用户组描述',
		  `rights` text NOT NULL COMMENT '权限',
		  PRIMARY KEY  (`ug_id`)
		) ENGINE=InnoDB COMMENT='用户组';
	");

    mysql_query("
		INSERT INTO `user_groups` (`ug_id`, `name`, `description`, `rights`) VALUES
		(2, 'Administrator', '<p>\r\n完全的管理权限</p>\r\n', 'a:21:{s:6:\"system\";s:2:\"on\";s:5:\"admin\";s:2:\"on\";s:11:\"app_extends\";s:2:\"on\";s:10:\"attributes\";s:2:\"on\";s:8:\"bulletin\";s:2:\"on\";s:10:\"categories\";s:2:\"on\";s:11:\"collections\";s:2:\"on\";s:8:\"contacts\";s:2:\"on\";s:9:\"countries\";s:2:\"on\";s:5:\"error\";s:2:\"on\";s:4:\"help\";s:2:\"on\";s:5:\"items\";s:2:\"on\";s:10:\"make_money\";s:2:\"on\";s:5:\"pages\";s:2:\"on\";s:8:\"payments\";s:2:\"on\";s:8:\"percents\";s:2:\"on\";s:5:\"qnews\";s:2:\"on\";s:4:\"quiz\";s:2:\"on\";s:7:\"reports\";s:2:\"on\";s:4:\"tags\";s:2:\"on\";s:5:\"users\";s:2:\"on\";}');
	");
	
	mysql_query("
		CREATE TABLE `users_status` (
	    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'ID',
	    `user_id` INT NOT NULL  COMMENT '用户ID',
	    `status` ENUM( 'freefile', 'featured' ) NOT NULL  COMMENT '宣传形式',
	    `datetime` DATETIME NOT NULL COMMENT '设置时间',
	    INDEX ( `user_id` )
	  ) ENGINE = MYISAM  COMMENT='用户作品在站内的宣传记录';
  ");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `withdraw` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'ID',
	  `user_id` int(11) NOT NULL COMMENT '提现发起者ID',
	  `amount` varchar(255) NOT NULL COMMENT '提现金额',
	  `method` varchar(255) NOT NULL COMMENT '提现方式',
	  `text` text NOT NULL COMMENT '到账账号',
	  `chinese` enum('false','iam','iamnot') NOT NULL default 'false' COMMENT '中国大陆纳税主体',
	  `cbn` varchar(255) NOT NULL COMMENT '组织机构代码',
	  `ccn` varchar(255) NOT NULL COMMENT '营业执照号码',
	  `datetime` datetime NOT NULL COMMENT '发起提现时间',
	  `paid` enum('true','false') NOT NULL default 'false' COMMENT '已处理',
	  `paid_datetime` datetime default NULL COMMENT '处理时间',
	  PRIMARY KEY  (`id`),
	  KEY `user_id` (`user_id`)
	) ENGINE=MyISAM;
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `users_status` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '用户ID',
		  `status` enum('freefile','featured') NOT NULL COMMENT '宣传形式',
		  `datetime` datetime NOT NULL COMMENT '设置时间',
		  PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=MyISAM  COMMENT='用户作品在站内的宣传记录';
	");
	
	mysql_query("
        INSERT INTO `contacts_categories` (`id`, `name`, `text`, `visible`, `order_index`) VALUES
         (1, '文件问题', '', 'true', 1),
         (2, '支付问题', '', 'true', 2),
         (3, '大额充值', '', 'true', 3),
         (4, '意见建议', '', 'true', 4);
	");

	mysql_query("
		ALTER TABLE `deposit` ADD `from_admin` TINYINT( 1 ) NOT NULL , ADD INDEX ( `from_admin` );
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `users_referals_count` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
		  `user_id` int(11) NOT NULL COMMENT '受推广而注册的用户ID',
		  `timestamp` int(11) NOT NULL COMMENT '时间',
		  PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=MyISAM COMMENT='用户推广记录';
	");
	
	mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`) VALUES (NULL, 'referal_sum', '10'), (NULL, 'referal_percent', '60');
	");
	
	mysql_query("
		ALTER TABLE `system` ADD `system` TINYINT( 1 ) NOT NULL DEFAULT '0';
	");
	
	mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`, `system`) VALUES (NULL, 'prepaid_price_discount', '2', '1'), (NULL, 'extended_price', '50', '1');
	");
	
	mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`, `system`) VALUES (NULL, 'no_exclusive_author_percent', '30', '1'), (NULL, 'exclusive_author_percent', '40', '1');
	");
	
	mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`, `system`) VALUES (NULL, 'site_logo', '', '1');
	");

    mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`, `system`) VALUES (NULL, 'send_mail', '1', '1');
	");
	mysql_query("
		UPDATE `system` SET `system` = 1 WHERE `key` IN ('meta_title','meta_keywords','meta_description','admin_mail','report_mail','referal_sum','referal_percent','prepaid_price_discount','extended_price','no_exclusive_author_percent','exclusive_author_percent','site_logo');
	");


mysql_query("
		ALTER TABLE `users` ADD `commission_percent` INT( 2 ) NOT NULL DEFAULT '0';
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `items_to_category` (
		  `item_id` int(11) NOT NULL,
		  `categories` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		  KEY `item_id` (`item_id`,`categories`)
		) ENGINE=MyISAM;
	");
	
	mysql_query("
		INSERT INTO `items_to_category` (`item_id`, `categories`) SELECT `id`, `categories` FROM `items`;
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `badges` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
		  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '徽章的名称或显示的内容',
		  `photo` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '图片',
		  `visible` enum('true','false') COLLATE utf8_unicode_ci NOT NULL COMMENT '可见',
		  `from` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '从…(某金额)',
		  `to` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '到…(某金额)',
		  `type` enum('other','buyers','authors','referrals','system') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'other' COMMENT '徽章类型',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  COMMENT='徽章';
	");
	
	mysql_query("
		ALTER TABLE `badges` ADD `sys_key` VARCHAR( 64 ) NOT NULL;
	");
	
	mysql_query("
INSERT INTO `badges` (`id`, `name`, `photo`, `visible`, `from`, `to`, `type`, `sys_key`) VALUES
(19, '已售出价值¥1,000 - ¥5,000的作品', 'sold_between_1000_and_5000_yuan.png', 'true', '1000', '5000', 'authors', ''),
(18, '已售出价值¥100 - ¥1,000的作品', 'sold_between_100_and_1000_yuan.png', 'true', '100', '1000', 'authors', ''),
(17, '已售出价值¥1 - ¥100的作品', 'sold_between_1_and_100_yuan.png', 'true', '1', '100', 'authors', ''),
(28, '已购买100至499件作品', 'bought_between_100_and_499_items.png', 'true', '100', '499', 'buyers', ''),
(27, '已购买50至99件作品', 'bought_between_50_and_99_items.png', 'true', '50', '99', 'buyers', ''),
(26, '已购买10至49件作品', 'bought_between_10_and_49_items.png', 'true', '10', '49', 'buyers', ''),
(25, '已购买1至9件作品', 'bought_between_1_and_9_items.png', 'true', '1', '9', 'buyers', ''),
(7, '赢过一次比赛', 'won_a_competition.png', 'true', '0', '0', 'other', ''),
(39, '担任过beta测试员', 'beta_tester.png', 'true', '0', '0', 'other', ''),
(5, '已有一件作品被推荐', 'item_was_featured.png', 'true', '0', '0', 'system', 'has_had_item_featured'),
(4, '在本站独家出售其作品的作者', 'exclusive_author.png', 'true', '0', '0', 'system', 'is_exclusive_author'),
(3, '位置', '', 'true', '0', '0', 'system', 'location_global_community'),
(2, '曾经被推荐过', 'author_was_featured.png', 'true', '0', '0', 'system', 'has_been_featured'),
(1, '贡献了一次当月免费文件', 'author_had_free_file.png', 'true', '0', '0', 'system', 'has_free_file_month'),
(20, '已售出价值¥5,000 - ¥10,000的作品', 'sold_between_5000_and_10000_yuan.png', 'true', '5000', '10000', 'authors', ''),
(21, '已售出价值¥10,000 - ¥50,000的作品', 'sold_between_10000_and_50000_yuan.png', 'true', '10000', '500000', 'authors', ''),
(22, '已售出价值¥50,000 - ¥100,000的作品', 'sold_between_50000_and_100000_yuan.png', 'true', '50000', '100000', 'authors', ''),
(23, '已售出价值¥100,000 - ¥250,000的作品', 'sold_between_100000_and_250000_yuan.png', 'true', '100000', '250000', 'authors', ''),
(24, '已售出价值¥250,000 - ¥1,000,000的作品', 'sold_between_250000_and_1000000_yuan.png', 'true', '250000', '1000000', 'authors', ''),
(29, '已购买500至999件作品', 'bought_between_500_and_999_items.png', 'true', '500', '999', 'buyers', ''),
(30, '已购买超过1000件作品', 'bought_between_1000_and_4999_items.png', 'true', '1000', '4999', 'buyers', ''),
(31, '已介绍了1 - 9个会员', 'referred_between_1_and_9_users.png', 'true', '1', '9', 'referrals', ''),
(32, '已介绍了10 - 49个会员', 'referred_between_10_and_49_users.png', 'true', '10', '49', 'referrals', ''),
(33, '已介绍了50 - 99个会员', 'referred_between_50_and_99_users.png', 'true', '50', '99', 'referrals', ''),
(34, '已介绍了100 - 199个会员', 'referred_between_100_and_199_users.png', 'true', '100', '199', 'referrals', ''),
(35, '已介绍了200 - 499个会员', 'referred_between_200_and_499_users.png', 'true', '200', '499', 'referrals', ''),
(36, '已介绍了500 - 999个会员', 'referred_between_500_and_999_users.png', 'true', '500', '999', 'referrals', ''),
(37, '已介绍了1,000 - 1999个会员', 'referred_between_1000_and_1999_users.png', 'true', '1000', '1999', 'referrals', ''),
(38, '已介绍了2,000+个会员', 'referred_more_than_2000_users.png', 'true', '2000', '4999', 'referrals', ''),
(40, '权威精英作者', 'power_elite_author.png', 'true', '0', '0', 'system', 'power_elite_author'),
(41, '精英作者', 'elite_author.png', 'true', '0', '0', 'system', 'elite_author'),
(42, '市场管理员', 'marketplace_manager.png', 'true', '0', '0', 'other', ''),
(43, '作品审核员', 'reviewer.png', 'true', '0', '0', 'other', ''),
(44, '已有一件作品被推荐至套装', 'author_had_bundled_file.png', 'true', '0', '0', 'other', ''),
(45, '支持与帮助人员', 'support.png', 'true', '0', '0', 'other', ''),
(46, '精英开发者之一', 'developer.png', 'true', '0', '0', 'other', ''),
(47, '爱心大使', 'community_ambassador.png', 'true', '0', '0', 'other', ''),
(48, '帮助站点打击过盗版', 'violation.png', 'true', '0', '0', 'other', ''),
(49, '超级版权监察员!', 'violation_gold.png', 'true', '0', '0', 'other', ''),
(50, '贡献了一个教程', 'contributed_a_tutorial.png', 'true', '0', '0', 'other', ''),
(51, '站点博主', 'blog_editor.png', 'true', '0', '0', 'other', ''),
(52, '社区管理员', 'community_manager.png', 'true', '0', '0', 'other', ''),
(53, '独家出售超级巨星之一', 'community_superstar.png', 'true', '0', '0', 'other', ''),
(54, '社区版主', 'community_mod.png', 'true', '0', '0', 'other', '');
	");
	
	mysql_query("
		ALTER TABLE `users` ADD `badges` TEXT NOT NULL;
	");

	mysql_query("
		ALTER TABLE `users_referals_count` ADD `referal_id` INT( 11 ) NOT NULL , ADD INDEX ( `referal_id` ); 
	");
	
	mysql_query("
		ALTER TABLE `users_referals_count` CHANGE `timestamp` `datetime` DATETIME NOT NULL;
	");
	
	mysql_query("
		ALTER TABLE `items` ADD `suggested_price` FLOAT NOT NULL AFTER `price`;
	");
	
	mysql_query("
		CREATE TABLE IF NOT EXISTS `qnews` (
		  `id` int(6) NOT NULL auto_increment COMMENT 'ID',
		  `name` varchar(250) character set utf8 NOT NULL default '' COMMENT '快讯名称',
		  `description` varchar(255) character set utf8 NOT NULL default '' COMMENT '快讯内容',
		  `url` varchar(255) character set utf8 NOT NULL default '' COMMENT '内部URL',
		  `photo` varchar(255) character set utf8 NOT NULL COMMENT '图片',
		  `visible` enum('true','false') character set utf8 NOT NULL default 'true' COMMENT '可见',
		  `order_index` int(11) NOT NULL default '0' COMMENT '排序',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB COMMENT='登入用户首页快讯';
	");

	mysql_query("
		CREATE TABLE `transaction_details` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `type` enum('deposit','withdraw','buy','sale_income','referal_income') DEFAULT 'deposit' COMMENT '类型',
		  `value` float DEFAULT NULL COMMENT '金额',
		  `info` varchar(255) DEFAULT NULL COMMENT '详情描述',
		  `time` datetime DEFAULT NULL COMMENT '时间',
		  `uid` int(11) DEFAULT NULL COMMENT '流动用户id',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='资金流动记录';
	");
	
	mysql_query("
     INSERT INTO `qnews` (`id`, `name`, `description`, `url`, `photo`, `visible`, `order_index`) VALUES
     (1, '用DigBank做数字内容的生意。', '这是一个示例新闻，你可以点击标题更换。', 'index.php/admin/?m=qnews&c=list', 'fd80c3904925cc129c975db0d348e8e1.jpg', 'true', 1);
	");
	
	
	mysql_query("
		INSERT INTO `countries` (`id`, `name`, `photo`, `visible`, `order_index`) VALUES
(1, '中国大陆', 'chn.png', 'true', 1),
(2, '中国香港', 'hkg.png', 'true', 2),
(3, '中国澳门', 'mac.png', 'true', 3),
(4, '中国台湾', 'twn.png', 'true', 4),
(5, '美利坚合众国', 'usa.png', 'true', 5);
	");
    mysql_query("
            CREATE TABLE IF NOT EXISTS `service` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_name` varchar(20) NOT NULL COMMENT '客服姓名',
              `email` varchar(30) DEFAULT NULL COMMENT '邮件',
              `info` varchar(255) DEFAULT NULL COMMENT '备注',
              `status` enum('true','false') DEFAULT 'true' COMMENT '状态',
              `time` int(11) DEFAULT NULL COMMENT '时间',
              `service_num` int(11) DEFAULT NULL,
              `service_status` tinyint(1) DEFAULT '1' COMMENT '服务状态（0：该轮已服务，1：该轮未服务）',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
        ");

    mysql_query("
            CREATE TABLE IF NOT EXISTS `service_relation` (
            `user_id` int(11) NOT NULL COMMENT '用户id',
            `service_user_id` int(11) NOT NULL COMMENT '客服id',
            PRIMARY KEY (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服用户关联表';
        ");
	
	mysql_query("	
           INSERT INTO `deposit` (`id`, `user_id`, `deposit`, `paid`, `datetime`, `from_admin`) VALUES
            (1, 1, 500, 'true', '2013-06-06 00:00:00', 0),
            (2, 1, 500, 'true', '2013-07-21 18:17:49', 1);
	");
	
	mysql_query("
       INSERT INTO `history` (`id`, `user_id`, `action`, `transaction_id`, `datetime`) VALUES
        (1, 1, 'Deposit 500¥', '1', '0000-00-00 00:00:00');
	");
	
	mysql_query("ALTER TABLE `system` ADD `group` VARCHAR( 128 ) NOT NULL ,ADD INDEX ( `group` );");
	mysql_query("UPDATE `system` SET `group` = 'config';");
	mysql_query("INSERT INTO `system` (`key`, `value`, `system`, `group`) VALUES ('chinabank_v_key', '1', '1', 'chinabank'), ('chinabank_v_mid', '1', '1', 'chinabank');");
	mysql_query("UPDATE `system` SET `group`='images', `system` = 0 WHERE `key` = 'site_logo';");

    //截图包上传

    mysql_query("insert into `app_extends`(`extend_name`,`state`,`m`,`c`)  VALUES  ('截图包上传',1,'uploads_extends','uploads_extends');");

    mysql_query("
		 alter table temp_items change theme_preview theme_preview TEXT;
	    ");

    mysql_query("
		 alter table temp_items ADD first_preview VARCHAR(255);
	    ");
    mysql_query("
		 CREATE TABLE IF NOT EXISTS upload_queue
        (
              id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
              item_id INT NOT NULL,
              dir VARCHAR(255) NOT NULL,
              type TINYINT NOT NULL,
              queue_type TINYINT NOT NULL,
              `key` VARCHAR(255) DEFAULT 'wait',
              user_id INT DEFAULT 0
            );
	    ");

    mysql_query("
		CREATE TABLE IF NOT EXISTS `preview` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `item_id` int(11) NOT NULL,
		  `dir` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");


/*邮件设置*/
mysql_query("
		INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'smtp_host', '', '1','mailconf');
	  ");
mysql_query("
            INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'smtp_from', '', '1','mailconf');
        ");

mysql_query("
            INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'smtp_port', '25', '1','mailconf');
        ");

mysql_query("
            INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'smtp_user', '', '1','mailconf');
        ");

mysql_query("
                INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'smtp_pass', '', '1','mailconf');
        ");
mysql_query("
                INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'smtp_from_name', '', '1','mailconf');
        ");
mysql_query("
                INSERT INTO `system` (`id`, `key`, `value`, `system`,`group`) VALUES (NULL, 'template', 'default', '1','template');
        ");




	
?>