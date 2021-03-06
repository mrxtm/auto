SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- COMPONENT_SETTINGS
-- ----------------------------
CREATE TABLE IF NOT EXISTS `COMPONENT_SETTINGS` (
  `COMPONENT` varchar(32) NOT NULL DEFAULT '',
  `FIELD` varchar(32) NOT NULL DEFAULT '',
  `VALUE` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`COMPONENT`,`FIELD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `COMPONENT_SETTINGS` VALUES ('models.list', 'TEMPLATE_TITLE', 'BRAND - MODELS_LIST');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('models.list', 'TEMPLATE_KEYWORDS', 'BRAND, MODELS_LIST');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('models.list', 'TEMPLATE_DESCRIPTION', 'BRAND');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('models.list', 'TEMPLATE_TITLE_H1', '#Model selection#');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('types.list', 'TEMPLATE_TITLE', 'BRAND + MODEL');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('types.list', 'TEMPLATE_KEYWORDS', 'BRAND, MODEL_YEAR');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('types.list', 'TEMPLATE_DESCRIPTION', 'BRAND - MODEL_YEAR');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('types.list', 'TEMPLATE_TITLE_H1', '#Type of engine#');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('sections.tree', 'TEMPLATE_TITLE', 'BRAND + MODEL TYPE');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('sections.tree', 'TEMPLATE_KEYWORDS', 'BRAND, MODEL, TYPE');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('sections.tree', 'TEMPLATE_DESCRIPTION', 'BRAND - MODEL TYPE_FULL');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('sections.tree', 'TEMPLATE_TITLE_H1', '#Select parts category#');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.section', 'TEMPLATE_TITLE', '#Search of parts# BRAND MODEL TYPE SECTION');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.section', 'TEMPLATE_KEYWORDS', 'BRAND, MODEL, TYPE, SECTION');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.section', 'TEMPLATE_DESCRIPTION', 'BRAND MODEL SECTION');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.section', 'TEMPLATE_TITLE_H1', '#Selection of parts#');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.number', 'TEMPLATE_TITLE', '#Search of parts#: NUMBER');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.number', 'TEMPLATE_KEYWORDS', null);
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.number', 'TEMPLATE_DESCRIPTION', null);
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.by.number', 'TEMPLATE_TITLE_H1', '#Search of parts#');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.detail', 'TEMPLATE_TITLE', 'NAME BRAND, #Number# NUMBER');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.detail', 'TEMPLATE_KEYWORDS', 'NAME, BRAND, NUMBER');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.detail', 'TEMPLATE_DESCRIPTION', null);
INSERT INTO `COMPONENT_SETTINGS` VALUES ('parts.detail', 'TEMPLATE_TITLE_H1', 'NUMBER (BRAND)  - NAME');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('manufacturers.list', 'TEMPLATE_TITLE_H1', '#Choosing a brand#');
INSERT INTO `COMPONENT_SETTINGS` VALUES ('manufacturers.list', 'TEMPLATE_DESCRIPTION', null);
INSERT INTO `COMPONENT_SETTINGS` VALUES ('manufacturers.list', 'TEMPLATE_KEYWORDS', null);
INSERT INTO `COMPONENT_SETTINGS` VALUES ('manufacturers.list', 'TEMPLATE_TITLE', '#Choosing a brand#');

-- ----------------------------
-- Table structure for `CONVERT_RULES`
-- ----------------------------
DROP TABLE IF EXISTS `CONVERT_RULES`;
CREATE TABLE `CONVERT_RULES` (
  `ID` int(6) NOT NULL AUTO_INCREMENT,
  `R_FIELD` varchar(32) NOT NULL,
  `R_FROM` varchar(128) NOT NULL DEFAULT '',
  `R_TO` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `BRAND_FROM` (`R_FROM`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of CONVERT_RULES
-- ----------------------------
INSERT INTO `CONVERT_RULES` VALUES ('1', 'BRAND', 'MERCEDES BENZ', 'MERCEDES-BENZ');
INSERT INTO `CONVERT_RULES` VALUES ('2', 'BRAND', 'ABS', 'A.B.S.');

-- ----------------------------
-- Table structure for `CURRENCY`
-- ----------------------------
DROP TABLE IF EXISTS `CURRENCY`;
CREATE TABLE `CURRENCY` (
  `ID` int(2) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(3) NOT NULL DEFAULT '',
  `RATE` float(12,5) DEFAULT NULL,
  `TEMPLATE` varchar(12) DEFAULT NULL,
  `SEPARATOR_TEN` varchar(1) DEFAULT NULL,
  `SEPARATOR_THO` varchar(1) DEFAULT NULL,
  `DECIMAL_PLACES` int(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of CURRENCY
-- ----------------------------
INSERT INTO `CURRENCY` VALUES ('1', 'USD', '0.02995', '#$', '', '', '2');
INSERT INTO `CURRENCY` VALUES ('2', 'EUR', '0.02274', '#e', '', '', '2');
INSERT INTO `CURRENCY` VALUES ('3', 'RUB', '1.00000', '# p.', '', '', '2');
INSERT INTO `CURRENCY` VALUES ('4', 'UAH', '0.24428', '# uah', '', '', '2');
INSERT INTO `CURRENCY` VALUES ('5', 'BYR', '271.33801', '# p.', '', '', '0');

-- ----------------------------
-- IMPORT_COLUMNS
-- ----------------------------
CREATE TABLE IF NOT EXISTS `IMPORT_COLUMNS` (
  `ID` int(6) NOT NULL AUTO_INCREMENT,
  `TEMPL_ID` int(6) NOT NULL,
  `CSV_NUM` int(3) NOT NULL,
  `TEMPL_FIELD` varchar(128) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `IMPORT_COLUMNS` VALUES ('1', '1', '1', 'ART_NUM');
INSERT INTO `IMPORT_COLUMNS` VALUES ('2', '1', '2', 'SUP_BRAND');
INSERT INTO `IMPORT_COLUMNS` VALUES ('3', '1', '3', 'PRICE');
INSERT INTO `IMPORT_COLUMNS` VALUES ('4', '1', '4', 'CURRENCY');
INSERT INTO `IMPORT_COLUMNS` VALUES ('5', '1', '5', 'DAY');
INSERT INTO `IMPORT_COLUMNS` VALUES ('6', '1', '6', 'AVAILABLE');
INSERT INTO `IMPORT_COLUMNS` VALUES ('7', '1', '7', 'SUPPLIER');
INSERT INTO `IMPORT_COLUMNS` VALUES ('8', '1', '8', 'STOCK');
INSERT INTO `IMPORT_COLUMNS` VALUES ('11', '1', '9', 'PART_NAME');

-- ----------------------------
-- IMPORT_EXGROUPS
-- ----------------------------
CREATE TABLE IF NOT EXISTS `IMPORT_EXGROUPS` (
  `ID` int(6) NOT NULL AUTO_INCREMENT,
  `IDEF` int(1) DEFAULT NULL,
  `NAME` varchar(128) NOT NULL,
  `RENGE` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `EXTRA` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `FIXED` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `IMPORT_EXGROUPS` VALUES ('1', '0', 'Default', '110/500/1000', '35/30/26', '2//');

-- ----------------------------
-- IMPORT_TEMPLATES
-- ----------------------------
CREATE TABLE IF NOT EXISTS `IMPORT_TEMPLATES` (
  `ID` int(6) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `IDEF` int(1) NOT NULL,
  `SEP` varchar(9) NOT NULL,
  `ENCODE` varchar(16) NOT NULL,
  `ITABLE` varchar(128) NOT NULL,
  `EXTRA` int(4) NOT NULL,
  `DEF_DAY` varchar(5) NOT NULL,
  `DEF_AVAIL` varchar(32) NOT NULL,
  `DEF_SUPL` varchar(64) NOT NULL,
  `DEF_STOCK` varchar(64) NOT NULL,
  `SEP_ART` varchar(9) NOT NULL,
  `DEF_BRA` varchar(64) NOT NULL,
  `PRICE_CONVERT` int(6) NOT NULL,
  `DEF_CUR` varchar(3) DEFAULT NULL,
  `DEF_ICODE` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `IMPORT_TEMPLATES` VALUES ('1', 'Test prices', '1', ';', 'CP1251', 'PRICES', '30', '1-2', '10', 'Alfocar', '2Sc', '', 'BOSH', '0', 'USD', 'k1');
INSERT INTO `IMPORT_TEMPLATES` VALUES ('2', 'Test crosses', '0', ';', 'CP1251', 'LINKS', '0', '', '0', '', '', '', '', '0', '', '');

-- ----------------------------
-- LINKS
-- ----------------------------
CREATE TABLE IF NOT EXISTS `LINKS` (
  `ID` int(9) NOT NULL AUTO_INCREMENT,
  `CROSS_NUMS` varchar(128) NOT NULL DEFAULT '',
  `CROSS_BRAND` varchar(128) DEFAULT NULL,
  `ORIGINAL_NUMS` varchar(128) NOT NULL DEFAULT '',
  `ORIGINAL_BRAND` varchar(128) DEFAULT NULL,
  `IMPORT_CODE` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`CROSS_NUMS`,`ORIGINAL_NUMS`),
  KEY `CROSS_NUMS` (`CROSS_NUMS`(18)),
  KEY `ORIGINAL_NUMS` (`ORIGINAL_NUMS`(18)),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `LINKS` VALUES ('1', '01103678A', 'GENUINE', '01103678A', 'CHEVROLET', null);
INSERT INTO `LINKS` VALUES ('2', 'CT637;9901149', 'AE', '43803', 'MAPCO', null);

-- ----------------------------
-- PRICES
-- ----------------------------
CREATE TABLE IF NOT EXISTS `PRICES` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `ART_NUM` varchar(128) NOT NULL DEFAULT '',
  `SUP_BRAND` varchar(128) NOT NULL DEFAULT '',
  `PART_NAME` varchar(512) DEFAULT NULL,
  `PRICE` float(9,2) DEFAULT NULL,
  `CURRENCY` varchar(3) DEFAULT NULL,
  `DAY` varchar(5) NOT NULL DEFAULT '',
  `AVAILABLE` varchar(6) DEFAULT NULL,
  `SUPPLIER` varchar(64) NOT NULL DEFAULT '',
  `STOCK` varchar(64) NOT NULL,
  `SEARCH_KEYWORDS` varchar(512) DEFAULT NULL,
  `IMPORT_CODE` varchar(12) DEFAULT NULL,
  `IMPORT_DATE` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ART_NUM`,`SUP_BRAND`,`DAY`,`SUPPLIER`,`STOCK`),
  KEY `ART_NUM` (`ART_NUM`) USING BTREE,
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of PRICES
-- ----------------------------
INSERT INTO `PRICES` VALUES ('1', 'CT637', 'AE', 'GRM belt', '20.00', 'RUB', '2', '12', 'Poland', '', '', 'k1', '1363890628');
INSERT INTO `PRICES` VALUES ('2', 'TB108', 'AE', '', '8.50', 'USD', '55', '3', 'Texcar', '2', '', 'k2', '1363890628');

-- ----------------------------
-- SEO_META_DATA
-- ----------------------------
CREATE TABLE IF NOT EXISTS `SEO_META_DATA` (
  `ID` int(9) NOT NULL AUTO_INCREMENT,
  `URL_PATH` varchar(128) DEFAULT NULL,
  `TITLE` varchar(128) DEFAULT NULL,
  `KEYWORDS` varchar(128) DEFAULT NULL,
  `DESCRIPTION` varchar(128) DEFAULT NULL,
  `TITLE_H1` varchar(128) DEFAULT NULL,
  `SEO_TOPTEXT` text,
  `SEO_BOTTEXT` text,
  PRIMARY KEY (`ID`),
  KEY `URL_PATH` (`URL_PATH`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

