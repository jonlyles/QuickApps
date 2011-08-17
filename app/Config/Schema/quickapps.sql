-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `quickapps`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__acos`
--

CREATE TABLE IF NOT EXISTS `#__acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=146 ;

--
-- Volcar la base de datos para la tabla `#__acos`
--

INSERT INTO `#__acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, NULL, NULL, 'Block', 1, 18),
(2, 1, NULL, NULL, 'Block', 2, 5),
(3, 2, NULL, NULL, 'admin_index', 3, 4),
(4, 1, NULL, NULL, 'Manage', 6, 17),
(5, 4, NULL, NULL, 'admin_index', 7, 8),
(6, 4, NULL, NULL, 'admin_move', 9, 10),
(7, 4, NULL, NULL, 'admin_edit', 11, 12),
(8, 4, NULL, NULL, 'admin_add', 13, 14),
(9, 4, NULL, NULL, 'admin_delete', 15, 16),
(10, NULL, NULL, NULL, 'Comment', 19, 32),
(11, 10, NULL, NULL, 'Comment', 20, 23),
(12, 11, NULL, NULL, 'admin_index', 21, 22),
(13, 10, NULL, NULL, 'Published', 24, 27),
(14, 13, NULL, NULL, 'admin_index', 25, 26),
(15, 10, NULL, NULL, 'Unpublished', 28, 31),
(16, 15, NULL, NULL, 'admin_index', 29, 30),
(17, NULL, NULL, NULL, 'Field', 33, 40),
(18, 17, NULL, NULL, 'Handler', 34, 39),
(19, 18, NULL, NULL, 'admin_delete', 35, 36),
(20, 18, NULL, NULL, 'admin_move', 37, 38),
(21, NULL, NULL, NULL, 'Locale', 41, 80),
(22, 21, NULL, NULL, 'Languages', 42, 53),
(23, 22, NULL, NULL, 'admin_index', 43, 44),
(24, 22, NULL, NULL, 'admin_set_default', 45, 46),
(25, 22, NULL, NULL, 'admin_add', 47, 48),
(26, 22, NULL, NULL, 'admin_edit', 49, 50),
(27, 22, NULL, NULL, 'admin_delete', 51, 52),
(28, 21, NULL, NULL, 'Locale', 54, 57),
(29, 28, NULL, NULL, 'admin_index', 55, 56),
(30, 21, NULL, NULL, 'Packages', 58, 67),
(31, 30, NULL, NULL, 'admin_index', 59, 60),
(32, 30, NULL, NULL, 'admin_download_package', 61, 62),
(33, 30, NULL, NULL, 'admin_uninstall', 63, 64),
(34, 30, NULL, NULL, 'admin_install', 65, 66),
(35, 21, NULL, NULL, 'Translations', 68, 79),
(36, 35, NULL, NULL, 'admin_index', 69, 70),
(37, 35, NULL, NULL, 'admin_list', 71, 72),
(38, 35, NULL, NULL, 'admin_edit', 73, 74),
(39, 35, NULL, NULL, 'admin_add', 75, 76),
(40, 35, NULL, NULL, 'admin_delete', 77, 78),
(41, NULL, NULL, NULL, 'Menu', 81, 104),
(42, 41, NULL, NULL, 'Manage', 82, 99),
(43, 42, NULL, NULL, 'admin_index', 83, 84),
(44, 42, NULL, NULL, 'admin_delete', 85, 86),
(45, 42, NULL, NULL, 'admin_add', 87, 88),
(46, 42, NULL, NULL, 'admin_edit', 89, 90),
(47, 42, NULL, NULL, 'admin_delete_link', 91, 92),
(48, 42, NULL, NULL, 'admin_add_link', 93, 94),
(49, 42, NULL, NULL, 'admin_edit_link', 95, 96),
(50, 42, NULL, NULL, 'admin_links', 97, 98),
(51, 41, NULL, NULL, 'Menu', 100, 103),
(52, 51, NULL, NULL, 'admin_index', 101, 102),
(53, NULL, NULL, NULL, 'Node', 105, 150),
(54, 53, NULL, NULL, 'Contents', 106, 119),
(55, 54, NULL, NULL, 'admin_index', 107, 108),
(56, 54, NULL, NULL, 'admin_edit', 109, 110),
(57, 54, NULL, NULL, 'admin_create', 111, 112),
(58, 54, NULL, NULL, 'admin_add', 113, 114),
(59, 54, NULL, NULL, 'admin_delete', 115, 116),
(60, 54, NULL, NULL, 'admin_clear_cache', 117, 118),
(61, 53, NULL, NULL, 'Node', 120, 129),
(62, 61, NULL, NULL, 'admin_index', 121, 122),
(63, 61, NULL, NULL, 'index', 123, 124),
(64, 61, NULL, NULL, 'details', 125, 126),
(65, 61, NULL, NULL, 'search', 127, 128),
(66, 53, NULL, NULL, 'Types', 130, 149),
(67, 66, NULL, NULL, 'admin_index', 131, 132),
(68, 66, NULL, NULL, 'admin_edit', 133, 134),
(69, 66, NULL, NULL, 'admin_add', 135, 136),
(70, 66, NULL, NULL, 'admin_delete', 137, 138),
(71, 66, NULL, NULL, 'admin_display', 139, 140),
(72, 66, NULL, NULL, 'admin_field_settings', 141, 142),
(73, 66, NULL, NULL, 'admin_field_formatter', 143, 144),
(74, 66, NULL, NULL, 'admin_fields', 145, 146),
(75, 66, NULL, NULL, 'admin_help', 147, 148),
(76, NULL, NULL, NULL, 'System', 151, 200),
(77, 76, NULL, NULL, 'Configuration', 152, 155),
(78, 77, NULL, NULL, 'admin_index', 153, 154),
(79, 76, NULL, NULL, 'Dashboard', 156, 159),
(80, 79, NULL, NULL, 'admin_index', 157, 158),
(81, 76, NULL, NULL, 'Help', 160, 165),
(82, 81, NULL, NULL, 'admin_index', 161, 162),
(83, 81, NULL, NULL, 'admin_module', 163, 164),
(84, 76, NULL, NULL, 'Modules', 166, 177),
(85, 84, NULL, NULL, 'admin_index', 167, 168),
(86, 84, NULL, NULL, 'admin_settings', 169, 170),
(87, 84, NULL, NULL, 'admin_toggle', 171, 172),
(88, 84, NULL, NULL, 'admin_uninstall', 173, 174),
(89, 84, NULL, NULL, 'admin_install', 175, 176),
(90, 76, NULL, NULL, 'Structure', 178, 181),
(91, 90, NULL, NULL, 'admin_index', 179, 180),
(92, 76, NULL, NULL, 'System', 182, 185),
(93, 92, NULL, NULL, 'admin_index', 183, 184),
(94, 76, NULL, NULL, 'Themes', 186, 199),
(95, 94, NULL, NULL, 'admin_index', 187, 188),
(96, 94, NULL, NULL, 'admin_set_theme', 189, 190),
(97, 94, NULL, NULL, 'admin_settings', 191, 192),
(98, 94, NULL, NULL, 'admin_uninstall', 193, 194),
(99, 94, NULL, NULL, 'admin_install', 195, 196),
(100, 94, NULL, NULL, 'admin_theme_tn', 197, 198),
(101, NULL, NULL, NULL, 'Taxonomy', 201, 224),
(102, 101, NULL, NULL, 'Taxonomy', 202, 205),
(103, 102, NULL, NULL, 'admin_index', 203, 204),
(104, 101, NULL, NULL, 'Vocabularies', 206, 223),
(105, 104, NULL, NULL, 'admin_index', 207, 208),
(106, 104, NULL, NULL, 'admin_add', 209, 210),
(107, 104, NULL, NULL, 'admin_move', 211, 212),
(108, 104, NULL, NULL, 'admin_edit', 213, 214),
(109, 104, NULL, NULL, 'admin_delete', 215, 216),
(110, 104, NULL, NULL, 'admin_terms', 217, 218),
(111, 104, NULL, NULL, 'admin_delete_term', 219, 220),
(112, 104, NULL, NULL, 'admin_edit_term', 221, 222),
(113, NULL, NULL, NULL, 'User', 225, 290),
(114, 113, NULL, NULL, 'Display', 226, 231),
(115, 114, NULL, NULL, 'admin_index', 227, 228),
(116, 114, NULL, NULL, 'admin_field_formatter', 229, 230),
(117, 113, NULL, NULL, 'Fields', 232, 237),
(118, 117, NULL, NULL, 'admin_index', 233, 234),
(119, 117, NULL, NULL, 'admin_field_settings', 235, 236),
(120, 113, NULL, NULL, 'List', 238, 251),
(121, 120, NULL, NULL, 'admin_index', 239, 240),
(122, 120, NULL, NULL, 'admin_delete', 241, 242),
(123, 120, NULL, NULL, 'admin_block', 243, 244),
(124, 120, NULL, NULL, 'admin_activate', 245, 246),
(125, 120, NULL, NULL, 'admin_add', 247, 248),
(126, 120, NULL, NULL, 'admin_edit', 249, 250),
(127, 113, NULL, NULL, 'Permissions', 252, 259),
(128, 127, NULL, NULL, 'admin_index', 253, 254),
(129, 127, NULL, NULL, 'admin_edit', 255, 256),
(130, 127, NULL, NULL, 'admin_toggle', 257, 258),
(131, 113, NULL, NULL, 'Roles', 260, 267),
(132, 131, NULL, NULL, 'admin_index', 261, 262),
(133, 131, NULL, NULL, 'admin_edit', 263, 264),
(134, 131, NULL, NULL, 'admin_delete', 265, 266),
(135, 113, NULL, NULL, 'User', 268, 289),
(136, 135, NULL, NULL, 'admin_index', 269, 270),
(137, 135, NULL, NULL, 'login', 271, 272),
(138, 135, NULL, NULL, 'logout', 273, 274),
(139, 135, NULL, NULL, 'admin_logout', 275, 276),
(140, 135, NULL, NULL, 'admin_login', 277, 278),
(141, 135, NULL, NULL, 'register', 279, 280),
(142, 135, NULL, NULL, 'activate', 281, 282),
(143, 135, NULL, NULL, 'password_recovery', 283, 284),
(144, 135, NULL, NULL, 'profile', 285, 286),
(145, 135, NULL, NULL, 'my_account', 287, 288);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__aros`
--

CREATE TABLE IF NOT EXISTS `#__aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `#__aros`
--

INSERT INTO `#__aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'User.Role', 1, NULL, 1, 2),
(2, NULL, 'User.Role', 2, NULL, 3, 4),
(3, NULL, 'User.Role', 3, NULL, 5, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__aros_acos`
--

CREATE TABLE IF NOT EXISTS `#__aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `_read` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `_update` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `_delete` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

--
-- Volcar la base de datos para la tabla `#__aros_acos`
--

INSERT INTO `#__aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 2, 63, '1', '1', '1', '1'),
(2, 3, 63, '1', '1', '1', '1'),
(3, 2, 64, '1', '1', '1', '1'),
(4, 3, 64, '1', '1', '1', '1'),
(5, 2, 65, '1', '1', '1', '1'),
(6, 3, 65, '1', '1', '1', '1'),
(7, 2, 137, '1', '1', '1', '1'),
(8, 3, 137, '1', '1', '1', '1'),
(9, 2, 138, '1', '1', '1', '1'),
(10, 2, 139, '1', '1', '1', '1'),
(11, 2, 140, '1', '1', '1', '1'),
(12, 3, 140, '1', '1', '1', '1'),
(13, 2, 141, '1', '1', '1', '1'),
(14, 3, 141, '1', '1', '1', '1'),
(15, 2, 142, '1', '1', '1', '1'),
(16, 3, 142, '1', '1', '1', '1'),
(17, 2, 143, '1', '1', '1', '1'),
(18, 3, 143, '1', '1', '1', '1'),
(19, 2, 144, '1', '1', '1', '1'),
(20, 2, 145, '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__blocks`
--

CREATE TABLE IF NOT EXISTS `#__blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key - Unique block ID.',
  `module` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The module from which the block originates; for example, ’user’ for the Who’s Online block, and ’block’ for any custom blocks.',
  `delta` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Unique ID for block within a module. Or menu_id',
  `themes_cache` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'store all themes that belongs to (see block_regions table)',
  `ordering` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Block enabled status. (1 = enabled, 0 = disabled)',
  `visibility` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Flag to indicate how to show blocks on pages. (0 = Show on all pages except listed pages, 1 = Show only on listed pages, 2 = Use custom PHP code to determine visibility)',
  `pages` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Contents of the "Pages" block; contains either a list of paths on which to include/exclude the block or PHP code, depending on "visibility" setting.',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Custom title for the block. (Empty string will use block default title, <none> will remove the title, text will cause block to use specified title.)',
  `locale` text COLLATE utf8_unicode_ci,
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores block settings, such as region and visibility...' AUTO_INCREMENT=16 ;

--
-- Volcar la base de datos para la tabla `#__blocks`
--

INSERT INTO `#__blocks` (`id`, `module`, `delta`, `themes_cache`, `ordering`, `status`, `visibility`, `pages`, `title`, `locale`, `settings`) VALUES
(1, 'user', 'login', '', 1, 0, 0, '', 'User Login', 'a:0:{}', ''),
(2, 'menu', 'navigation', 'Default', 2, 1, 0, '', '', NULL, ''),
(3, 'system', 'powered_by', 'AdminDefault\r\nDefault', 1, 1, 0, '', 'Powered By', 'a:0:{}', ''),
(4, 'menu', 'management', 'AdminDefault', 1, 1, 1, '/admin/*', '', 'a:0:{}', ''),
(5, 'menu', 'user-menu', 'Default', 4, 1, 0, '', 'User Menu', 'a:0:{}', ''),
(6, 'menu', 'main-menu', 'Default', 1, 1, 0, '', '', 'a:0:{}', ''),
(7, 'user', 'new', 'AdminDefault', 5, 1, 0, '', 'New Users', 'a:0:{}', 'a:1:{s:10:"show_limit";s:1:"5";}'),
(9, 'locale', 'language_switcher', 'Default', 3, 1, 0, '', 'Language switcher', 'a:0:{}', 'a:2:{s:5:"flags";s:1:"1";s:4:"name";s:1:"1";}'),
(10, 'system', 'recent_content', 'AdminDefault', 1, 1, 0, '', 'Updates', 'a:0:{}', ''),
(11, 'block', '5', 'Default', 1, 1, 0, '', 'WHAT WE DO', 'a:0:{}', ''),
(12, 'block', '6', 'Default', 1, 1, 0, '', 'OUR MISSION', 'a:0:{}', ''),
(13, 'block', '7', 'Default', 1, 1, 0, '', 'WHO WE ARE', 'a:0:{}', ''),
(14, 'theme_default', 'slider', 'Default', 1, 1, 1, '/', 'Slider', 'a:0:{}', 'a:1:{s:12:"slider_order";s:52:"1_[language].jpg\r\n2_[language].jpg\r\n3_[language].jpg";}'),
(15, 'node', 'search', 'AdminDefault\nDefault', 1, 1, 0, '', 'Search', 'a:0:{}', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__block_custom`
--

CREATE TABLE IF NOT EXISTS `#__block_custom` (
  `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The block’s block.bid.',
  `body` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Block contents.',
  `description` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Block description.',
  `format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'The filter_format.format of the block body.',
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores contents of custom-made blocks.' AUTO_INCREMENT=14 ;

--
-- Volcar la base de datos para la tabla `#__block_custom`
--

INSERT INTO `#__block_custom` (`block_id`, `body`, `description`, `format`) VALUES
(11, '<p>Duis tellus nunc, egestas a interdum sed, congue vitae magna. Curabitur a tellus quis lacus blandit sagittis a sit amet elit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas metus sed neque ultricies.\r\n[button size=small color=silver]Read more[/button]</p>\r\n', 'Services-LEFT', NULL),
(12, '<p>Integer egestas ultricies urna vitae molestie. Donec nec facilisis nisi. Vivamus tempor feugiat velit gravida vehicula. Donec faucibus pellentesque ipsum id varius. Ut rutrum metus sed neque ultricies a dictum ante sagittis.\r\n[button size=small color=silver]Read more[/button]</p>\r\n', 'Services-CENTER', NULL),
(13, '<p>Praesent et metus sit amet nisl luctus commodo ut a risus. Mauris vehicula, ligula quis consectetur feugiat, arcu nibh tempor nisi, at varius dolor dolor nec dolor. Donec auctor mi vitae neque. Praesent sollicitudin egestas felis vitae gravida.\r\n[button size=small color=silver]Read more[/button]\r\n</p>\r\n', 'Services-RIGHT', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__block_regions`
--

CREATE TABLE IF NOT EXISTS `#__block_regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `theme` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=173 ;

--
-- Volcar la base de datos para la tabla `#__block_regions`
--

INSERT INTO `#__block_regions` (`id`, `block_id`, `theme`, `region`, `ordering`) VALUES
(8, 10, 'AdminDefault', 'dashboard_main', 1),
(9, 10, 'Default', 'dashboard_main', 1),
(13, 4, 'AdminDefault', 'management-menu', 1),
(14, 4, 'Default', 'management-menu', 1),
(18, 3, 'AdminDefault', 'footer', 1),
(48, 3, 'Default', 'footer', 1),
(131, 6, 'Default', 'main-menu', 1),
(133, 26, 'Default', 'slider', 1),
(140, 9, 'Default', 'language-switcher', 1),
(151, 19, 'Default', 'services-left', 1),
(153, 20, 'Default', 'services-center', 1),
(155, 25, 'Default', 'services-right', 1),
(157, 15, 'Default', 'search', 1),
(159, 12, 'Default', 'services-left', 2),
(161, 11, 'Default', 'services-center', 2),
(163, 13, 'Default', 'services-right', 2),
(165, 14, 'Default', 'slider', 2),
(166, 15, 'AdminDefault', 'dashboard_sidebar', 1),
(169, 7, 'AdminDefault', 'dashboard_sidebar', 2),
(172, 5, 'Default', 'user-menu', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__block_roles`
--

CREATE TABLE IF NOT EXISTS `#__block_roles` (
  `block_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `user_role_id` int(10) unsigned NOT NULL COMMENT 'The user’s role ID from users_roles.rid.',
  PRIMARY KEY (`block_id`,`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sets up access permissions for blocks based on user roles';

--
-- Volcar la base de datos para la tabla `#__block_roles`
--

INSERT INTO `#__block_roles` (`block_id`, `user_role_id`) VALUES
('1', 3),
('5', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__comments`
--

CREATE TABLE IF NOT EXISTS `#__comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique comment ID.',
  `node_id` int(11) NOT NULL DEFAULT '0' COMMENT 'The node.nid to which this comment is a reply.',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'The users.uid who authored the comment. If set to 0, this comment was created by an anonymous user.',
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hostname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The author’s host name. (IP)',
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` int(11) NOT NULL DEFAULT '0' COMMENT 'The time that the comment was created, as a Unix timestamp.',
  `modified` int(11) NOT NULL DEFAULT '0' COMMENT 'The time that the comment was last edited, as a Unix timestamp.',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'The published status of a comment. (0 = Not Published, 1 = Published)',
  `name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'The comment author’s name. Uses users.name if the user is logged in, otherwise uses the value typed into the comment form.',
  `mail` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'The comment author’s e-mail address from the comment form, if user is anonymous, and the ’Anonymous users may/must leave their contact information’ setting is turned on.',
  PRIMARY KEY (`id`),
  KEY `comment_status_pid` (`status`),
  KEY `comment_num_new` (`node_id`,`status`,`created`,`id`),
  KEY `comment_uid` (`user_id`),
  KEY `comment_nid_language` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores comments and associated data.' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__comments`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__fields`
--

CREATE TABLE IF NOT EXISTS `#__fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The primary identifier for a field',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The name of this field.  Must be unique',
  `label` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Human name',
  `belongsTo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field_module` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The module that implements the field object',
  `description` text COLLATE utf8_unicode_ci,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `settings` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Rendering settings (View mode)',
  `ordering` int(11) DEFAULT '1' COMMENT 'edit form ordering',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Fields instances' AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `#__fields`
--

INSERT INTO `#__fields` (`id`, `name`, `label`, `belongsTo`, `field_module`, `description`, `required`, `settings`, `ordering`) VALUES
(1, 'body', 'Body', 'NodeType-page', 'field_textarea', '', 1, 'a:3:{s:7:"display";a:4:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:4:"full";s:8:"settings";a:0:{}s:8:"ordering";i:1;s:11:"trim_length";s:3:"180";}s:4:"full";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:4:"full";s:8:"settings";a:0:{}s:8:"ordering";i:0;s:11:"trim_length";s:3:"600";}s:4:"list";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:7:"trimmed";s:8:"settings";a:0:{}s:8:"ordering";i:0;s:11:"trim_length";s:3:"400";}s:3:"rss";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:7:"trimmed";s:8:"settings";a:0:{}s:8:"ordering";i:0;s:11:"trim_length";s:3:"400";}}s:4:"type";s:1:"2";s:11:"text_format";s:4:"full";}', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__field_data`
--

CREATE TABLE IF NOT EXISTS `#__field_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `foreignKey` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `belongsTo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `#__field_data`
--

INSERT INTO `#__field_data` (`id`, `field_id`, `foreignKey`, `belongsTo`, `data`) VALUES
(1, 1, '1', 'Node', '<h3>Content Boxes</h3>\r\n<p>\r\n	[content_box type=success]Maecenas pellentesque cursus auctor.[/content_box]</p>\r\n<p>\r\n	[content_box type=error]Nam sagittis nisl non turpis aliquam mollis. Suspendisse ac metus nisi, sed vulputate arcu.[/content_box]</p>\r\n<p>\r\n	[content_box type=alert]Cras interdum leo quis arcu sagittis pulvinar. Curabitur suscipit vulputate erat eu rhoncus. Morbi facilisis mi in ligula ornare ultricies.[/content_box]</p>\r\n<p>\r\n	[content_box type=bubble]Fusce interdum cursus turpis vitae gravida. Aenean aliquet venenatis posuere. Etiam gravida ullamcorper purus.[/content_box]</p>\r\n<hr />\r\n<h3>\r\n	Buttons</h3>\r\n<p>\r\n	Using buttons hookTags, you can easily create a variety of buttons. These buttons all stem from a single tag, but vary in color and size (each of which are adjustable using color=&rdquo;&quot; and size=&rdquo;&quot; parameters).<br />\r\n	Allowed parameters:</p>\r\n<ol>\r\n	<li>\r\n		<strong>size:</strong> big, small</li>\r\n	<li>\r\n		<strong>color:</strong>\r\n		<ul>\r\n			<li>\r\n				small: black, blue, green, lightblue, orange, pink, purple, red, silver, teal</li>\r\n			<li>\r\n				big: blue, green, orange, purple, red, turquoise</li>\r\n		</ul>\r\n	</li>\r\n	<li>\r\n		<strong>link:</strong> url of your button</li>\r\n	<li>\r\n		<strong>target:</strong> open link en new window (_blank), open in same window (_self or unset parameter)</li>\r\n</ol>\r\n<h4>\r\n	&nbsp;</h4>\r\n<p>\r\n	&nbsp;</p>\r\n<h4>\r\n	Small Buttons</h4>\r\n<table style="width: 478px; height: 25px;">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				[button color=black]Button text[/button]</td>\r\n			<td>\r\n				[button color=blue]Button text[/button]</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				[button color=green]Button text[/button]</td>\r\n			<td>\r\n				[button color=lightblue]Button text[/button]</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				[button color=orange]Button text[/button]</td>\r\n			<td>\r\n				[button color=pink]Button text[/button]</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				[button color=purple]Button text[/button]</td>\r\n			<td>\r\n				[button color=red]Button text[/button]</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				[button color=silver]Button text[/button]</td>\r\n			<td>\r\n				[button color=teal]Button text[/button]</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<h4>\r\n	&nbsp;</h4>\r\n<p>\r\n	&nbsp;</p>\r\n<h4>\r\n	Big Buttons</h4>\r\n<table style="width: 478px; height: 25px;">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				[button color=blue size=big]Button text[/button]</td>\r\n			<td>\r\n				[button color=green size=big]Button text[/button]</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				[button color=orange size=big]Button text[/button]</td>\r\n			<td>\r\n				[button color=purple size=big]Button text[/button]</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				[button color=red size=big]Button text[/button]</td>\r\n			<td>\r\n				[button color=turquoise size=big]Button text[/button]</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n'),
(2, 1, '2', 'Node', 'Nam in iaculis lectus? Sed egestas dui quis leo porttitor vitae bibendum ipsum ultrices. Mauris nisi nulla, volutpat vel vestibulum non, lobortis sed lectus. Integer quis volutpat.\r\n[t=Hola mundo Original]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__i18n`
--

CREATE TABLE IF NOT EXISTS `#__i18n` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `locale` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `locale` (`locale`),
  KEY `model` (`model`),
  KEY `row_id` (`foreign_key`),
  KEY `field` (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__i18n`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__languages`
--

CREATE TABLE IF NOT EXISTS `#__languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language code, e.g. ’eng’',
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language name in English.',
  `native` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Native language name.',
  `direction` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ltr' COMMENT 'Direction of language (Left-to-Right , Right-to-Left ).',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT 'Enabled flag (1 = Enabled, 0 = Disabled).',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Weight, used in lists of languages.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of all available languages in the system.' AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `#__languages`
--

INSERT INTO `#__languages` (`id`, `code`, `name`, `native`, `direction`, `icon`, `status`, `ordering`) VALUES
(1, 'eng', 'English', 'English', 'ltr', 'us.gif', 1, 0),
(2, 'spa', 'Spanish', 'Español', 'ltr', 'es.gif', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__menus`
--

CREATE TABLE IF NOT EXISTS `#__menus` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique key for menu. This is used as a block delta so length is 32.',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Menu title; displayed at top of block.',
  `description` text COLLATE utf8_unicode_ci COMMENT 'Menu description.',
  `locale` text COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcar la base de datos para la tabla `#__menus`
--

INSERT INTO `#__menus` (`id`, `title`, `description`, `locale`, `module`) VALUES
('main-menu', 'Main menu', 'The <em>Main</em> menu is used on many sites to show the major sections of the site, often in a top navigation bar.', 'a:0:{}', 'system'),
('management', 'Management', 'The <em>Management</em> menu contains links for administrative tasks.', '', 'system'),
('navigation', 'Navigation', 'The <em>Navigation</em> menu contains links intended for site visitors. Links are added to the <em>Navigation</em> menu automatically by some modules.', '', 'system'),
('user-menu', 'User menu', 'The <em>User</em> menu contains links related to the user''s account, as well as the ''Log out'' link.', 'a:0:{}', 'system');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__menu_links`
--

CREATE TABLE IF NOT EXISTS `#__menu_links` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The menu name. All links with the same menu name (such as ’navigation’) are part of the same menu.',
  `lft` int(11) NOT NULL,
  `rght` int(11) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The parent link ID (plid) is the mlid of the link above in the hierarchy, or zero if the link is at the top level in its menu.',
  `link_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'external path',
  `router_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'internal path',
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The text displayed for the link, which may be modified by a title callback stored in menu_router.',
  `options` text COLLATE utf8_unicode_ci COMMENT 'A serialized array of options to be passed to the url() or l() function, such as a query string or HTML attributes.',
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'system' COMMENT 'The name of the module that generated this link.',
  `external` smallint(6) NOT NULL DEFAULT '0' COMMENT 'A flag to indicate if the link points to a full URL starting with a protocol, like http:// (1 = external, 0 = internal).',
  `expanded` tinyint(6) NOT NULL DEFAULT '0' COMMENT 'Flag for whether this link should be rendered as expanded in menus - expanded links always have their child links displayed, instead of only when the link is in the active trail (1 = expanded, 0 = not expanded)',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `path_menu` (`menu_id`),
  KEY `router_path` (`router_path`(128))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains the individual links within a menu.' AUTO_INCREMENT=24 ;

--
-- Volcar la base de datos para la tabla `#__menu_links`
--

INSERT INTO `#__menu_links` (`id`, `menu_id`, `lft`, `rght`, `parent_id`, `link_path`, `router_path`, `description`, `link_title`, `options`, `module`, `external`, `expanded`, `status`) VALUES
(1, 'management', 1, 2, 0, NULL, '/admin/system/dashboard', NULL, 'Dashboard', NULL, 'system', 0, 0, 1),
(2, 'management', 3, 12, 0, NULL, '/admin/system/structure', NULL, 'Structure', NULL, 'system', 0, 0, 1),
(3, 'management', 13, 14, 0, NULL, '/admin/node/contents', NULL, 'Content', NULL, 'system', 0, 0, 1),
(4, 'management', 15, 16, 0, NULL, '/admin/system/themes', NULL, 'Appearance', NULL, 'system', 0, 0, 1),
(5, 'management', 17, 18, 0, NULL, '/admin/system/modules', NULL, 'Modules', NULL, 'system', 0, 0, 1),
(6, 'management', 19, 20, 0, NULL, '/admin/user', NULL, 'Users', NULL, 'system', 0, 0, 1),
(7, 'management', 23, 24, 0, NULL, '/admin/system/configuration', NULL, 'Configuration', NULL, 'system', 0, 0, 1),
(8, 'management', 25, 26, 0, NULL, '/admin/system/help', NULL, 'Help', NULL, 'system', 0, 0, 1),
(9, 'management', 4, 5, 2, NULL, '/admin/block', 'Configure what block content appears in your site''s sidebars and other regions.', 'Blocks', NULL, 'system', 0, 0, 1),
(10, 'management', 6, 7, 2, NULL, '/admin/node/types', 'Manage content types.', 'Content Types', NULL, 'system', 0, 0, 1),
(11, 'management', 8, 9, 2, NULL, '/admin/menu', 'Add new menus to your site, edit existing menus, and rename and reorganize menu links.', 'Menus', NULL, 'system', 0, 0, 1),
(12, 'management', 10, 11, 2, NULL, '/admin/taxonomy', 'Manage tagging, categorization, and classification of your content.', 'Taxonomy', NULL, 'system', 0, 0, 1),
(13, 'main-menu', 3, 4, 0, NULL, '/d/hook-tags', '', 'Hook Tags', NULL, 'menu', 0, 0, 1),
(17, 'main-menu', 5, 6, 0, NULL, '/d/about', '', 'About', NULL, 'menu', 0, 0, 1),
(18, 'management', 21, 22, 0, NULL, '/admin/locale', '', 'Languages', NULL, 'locale', 0, 0, 1),
(21, 'main-menu', 1, 2, 0, NULL, '/', '', 'Home', NULL, 'menu', 0, 0, 1),
(22, 'user-menu', 1, 2, 0, NULL, '/user/my_account', '', 'My account', NULL, 'menu', 0, 0, 1),
(23, 'user-menu', 3, 4, 0, NULL, '/user/logout', '', 'Logout', NULL, 'menu', 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__modules`
--

CREATE TABLE IF NOT EXISTS `#__modules` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'machine name',
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'module or theme',
  `settings` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized extra data',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcar la base de datos para la tabla `#__modules`
--

INSERT INTO `#__modules` (`name`, `type`, `settings`, `status`) VALUES
('block', 'module', '', 1),
('comment', 'module', '', 1),
('field', 'module', '', 1),
('locale', 'module', '', 1),
('menu', 'module', '', 1),
('node', 'module', '', 1),
('system', 'module', '', 1),
('taxonomy', 'module', '', 1),
('theme_admin_default', 'theme', 'a:4:{s:9:"site_logo";s:1:"1";s:9:"site_name";s:1:"1";s:11:"site_slogan";s:1:"1";s:12:"site_favicon";s:1:"1";}', 1),
('theme_default', 'theme', 'a:7:{s:13:"slider_folder";s:6:"slider";s:9:"site_logo";s:1:"1";s:9:"site_name";s:1:"0";s:11:"site_slogan";s:1:"1";s:12:"site_favicon";s:1:"1";s:16:"color_header_top";s:7:"#282727";s:19:"color_header_bottom";s:7:"#332f2f";}', 1),
('user', 'module', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__nodes`
--

CREATE TABLE IF NOT EXISTS `#__nodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The primary identifier for a node.',
  `node_type_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The node_type.type of this node.',
  `node_type_base` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'performance data for models',
  `language` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The languages.language of this node.',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The title of this node, always treated as non-markup plain text.',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci NOT NULL,
  `terms_cache` text COLLATE utf8_unicode_ci COMMENT 'serialized data for find performance',
  `roles_cache` text COLLATE utf8_unicode_ci COMMENT 'serialized data for find performance',
  `created_by` int(11) NOT NULL DEFAULT '0' COMMENT 'The users.uid that owns this node; initially, this is the user that created it.',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT 'Boolean indicating whether the node is published (visible to non-administrators).',
  `created` int(11) NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp when the node was created.',
  `modified` int(11) NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp when the node was most recently saved.',
  `modified_by` int(11) DEFAULT NULL,
  `comment` int(11) NOT NULL DEFAULT '0' COMMENT 'Whether comments are allowed on this node: 0 = no, 1 = closed (read only), 2 = open (read/write).',
  `comment_count` int(11) DEFAULT '0',
  `promote` int(11) NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether the node should be displayed on the front page.',
  `sticky` int(11) NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether the node should be displayed at the top of lists in which it appears.',
  `cache` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The base table for nodes.' AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `#__nodes`
--

INSERT INTO `#__nodes` (`id`, `node_type_id`, `node_type_base`, `language`, `title`, `description`, `slug`, `terms_cache`, `roles_cache`, `created_by`, `status`, `created`, `modified`, `modified_by`, `comment`, `comment_count`, `promote`, `sticky`, `cache`) VALUES
(1, 'page', 'node_content', '', 'Hook Tags', '', 'hook-tags', '', '', 1, 1, 1310424311, 1310424311, 1, 0, 0, 0, 0, ''),
(2, 'page', 'node_content', '', 'About', '', 'about', '', '', 1, 1, 1310424311, 1310424311, 1, 0, 1, 1, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__nodes_roles`
--

CREATE TABLE IF NOT EXISTS `#__nodes_roles` (
  `node_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(10) unsigned NOT NULL COMMENT 'The user’s role ID from roles.id.',
  PRIMARY KEY (`node_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sets up access permissions for blocks based on user roles';

--
-- Volcar la base de datos para la tabla `#__nodes_roles`
--

INSERT INTO `#__nodes_roles` (`node_id`, `role_id`) VALUES
('1', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__nodes_terms`
--

CREATE TABLE IF NOT EXISTS `#__nodes_terms` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node_id` int(20) NOT NULL DEFAULT '0',
  `term_id` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__nodes_terms`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__node_types`
--

CREATE TABLE IF NOT EXISTS `#__node_types` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The machine-readable name of this type.',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The human-readable name of this type.',
  `base` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The base string used to construct callbacks corresponding to this node type.',
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The module defining this node type.',
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'A brief description of this type.',
  `title_label` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The label displayed for the title field on the edit form.',
  `comments_approve` tinyint(1) DEFAULT '0',
  `comments_per_page` int(4) NOT NULL DEFAULT '10',
  `comments_anonymous` tinyint(3) NOT NULL DEFAULT '0',
  `comments_subject_field` tinyint(1) NOT NULL DEFAULT '1',
  `node_show_author` tinyint(1) DEFAULT '1',
  `node_show_date` tinyint(1) DEFAULT '1',
  `default_comment` int(11) DEFAULT NULL,
  `default_language` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_status` int(11) DEFAULT NULL,
  `default_promote` int(11) DEFAULT NULL,
  `default_sticky` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether the node type is disabled.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores information about all defined node types.';

--
-- Volcar la base de datos para la tabla `#__node_types`
--

INSERT INTO `#__node_types` (`id`, `name`, `base`, `module`, `description`, `title_label`, `comments_approve`, `comments_per_page`, `comments_anonymous`, `comments_subject_field`, `node_show_author`, `node_show_date`, `default_comment`, `default_language`, `default_status`, `default_promote`, `default_sticky`, `status`) VALUES
('page', 'Basic page', 'node_content', 'system', 'Use <em>basic pages</em> for your static content, such as an ''About us'' page.', 'Title', 1, 10, 2, 1, 0, 0, 0, 'es', 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__roles`
--

CREATE TABLE IF NOT EXISTS `#__roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `#__roles`
--

INSERT INTO `#__roles` (`id`, `name`, `ordering`) VALUES
(1, 'administrator', 1),
(2, 'authenticated user', 2),
(3, 'anonymous user', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__terms`
--

CREATE TABLE IF NOT EXISTS `#__terms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `vocabulary_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `modified` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rght` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__terms`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__translations`
--

CREATE TABLE IF NOT EXISTS `#__translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__translations`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__types_vocabularies`
--

CREATE TABLE IF NOT EXISTS `#__types_vocabularies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `node_type_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `vocabulary_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='NodeType HABTM Vocabulary' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__types_vocabularies`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__users`
--

CREATE TABLE IF NOT EXISTS `#__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` tinytext COLLATE utf8_unicode_ci NOT NULL COMMENT 'full url to avatar image file',
  `language` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__users_roles`
--

CREATE TABLE IF NOT EXISTS `#__users_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User HABTM Role' AUTO_INCREMENT=9 ;

--
-- Volcar la base de datos para la tabla `#__users_roles`
--

INSERT INTO `#__users_roles` (`id`, `user_id`, `role_id`) VALUES
(8, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__variables`
--

CREATE TABLE IF NOT EXISTS `#__variables` (
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_2` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcar la base de datos para la tabla `#__variables`
--

INSERT INTO `#__variables` (`name`, `value`) VALUES
('admin_theme', 's:12:"AdminDefault";'),
('date_default_timezone', 's:13:"Europe/Madrid";'),
('default_language', 's:3:"eng";'),
('default_nodes_main', 's:1:"8";'),
('failed_login_limit', 'i:5;'),
('rows_per_page', 'i:10;'),
('site_description', 'a:0:{}'),
('site_frontpage', 'a:0:{}'),
('site_logo', 's:8:"logo.gif";'),
('site_mail', 's:24:"no-reply@your-domain.com";'),
('site_name', 's:17:"My QuickApps Site";'),
('site_online', 's:1:"1";'),
('site_slogan', 's:142:"Vivamus id feugiat ligula. Nulla facilisi. Integer lacus justo, elementum eget consequat a, molestie nec sapien. Quisque tincidunt, nunc vitae";'),
('site_theme', 's:7:"Default";'),
('user_default_avatar', 's:25:"/img/anonymous_avatar.jpg";'),
('user_mail_activation_body', 's:246:"[user_name],\r\n\r\nYour account at [site_name] has been activated.\r\n\r\nYou may now log in by clicking this link or copying and pasting it into your browser:\r\n\r\n[site_login_url]\r\n\r\nusername: [user_name]\r\npassword: Your password\r\n\r\n--  [site_name] team";'),
('user_mail_activation_notify', 's:1:"1";'),
('user_mail_activation_subject', 's:57:"Account details for [user_name] at [site_name] (approved)";'),
('user_mail_blocked_body', 's:85:"[user_name],\r\n\r\nYour account on [site_name] has been blocked.\r\n\r\n--  [site_name] team";'),
('user_mail_blocked_notify', 's:1:"1";'),
('user_mail_blocked_subject', 's:56:"Account details for [user_name] at [site_name] (blocked)";'),
('user_mail_canceled_body', 's:86:"[user_name],\r\n\r\nYour account on [site_name] has been canceled.\r\n\r\n--  [site_name] team";'),
('user_mail_canceled_notify', 'a:0:{}'),
('user_mail_canceled_subject', 's:57:"Account details for [user_name] at [site_name] (canceled)";'),
('user_mail_password_recovery_body', 's:273:"[user_name],\r\n\r\nA request to reset the password for your account has been made at [site_name].\r\nYou may now log in by clicking this link or copying and pasting it to your browser:\r\n\r\n[user_activation_url]\r\n\r\nAfter log in you can reset your password.\r\n\r\n--  [site_name] team";'),
('user_mail_password_recovery_subject', 's:60:"Replacement login information for [user_name] at [site_name]";'),
('user_mail_welcome_body', 's:301:"[user_name],\r\n\r\nThank you for registering at [site_name]. You may now activate your account by clicking this link or copying and pasting it to your browser:\r\n\r\n[user_activation_url]\r\n\r\nThis link can only be used once to log in.\r\n\r\nusername: [user_name]\r\npassword: Your password\r\n\r\n--  [site_name] team";'),
('user_mail_welcome_subject', 's:46:"Account details for [user_name] at [site_name]";');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__vocabularies`
--

CREATE TABLE IF NOT EXISTS `#__vocabularies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `modified` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `#__vocabularies`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
