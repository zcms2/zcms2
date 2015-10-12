CREATE TABLE IF NOT EXISTS bug_tracking (
  id                       INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  bug_tracking_priority_id INTEGER,
  bug_tracking_type_id     INTEGER,
  bug_tracking_status_id   INTEGER DEFAULT 1,
  role_id                  INTEGER,
  description              TEXT,
  image                    TEXT,
  fixed_at                 DATETIME,
  fixed_by                 INTEGER,
  created_at               DATETIME,
  created_by               INTEGER,
  updated_at               DATETIME,
  updated_by               INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS bug_tracking_priority (
  id       INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name     VARCHAR(255),
  ordering INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS bug_tracking_status (
  id       INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name     VARCHAR(255),
  ordering INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS bug_tracking_type (
  id       INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name     VARCHAR(255),
  ordering INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE core_options
(
  option_id    BIGINT      NOT NULL PRIMARY KEY AUTO_INCREMENT,
  option_scope VARCHAR(64),
  option_name  VARCHAR(64) NOT NULL UNIQUE,
  option_value LONGTEXT,
  autoload     SMALLINT    NOT NULL             DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
INSERT INTO core_options (option_scope, option_name, option_value, autoload) VALUES
  ('zcms', 'auto_login_if_account_exists_with_facebook', '0', 1),
  ('zcms', 'auto_login_if_account_exists_with_google', '0', 1),
  ('zcms', 'auto_login_if_account_exists_with_twitter', '0', 1),
  ('zcms', 'verify_register_or_exist_account_with_facebook', '0', 1),
  ('zcms', 'verify_register_or_exist_account_with_google', '0', 1),
  ('zcms', 'verify_register_or_exist_account_with_twitter', '0', 1),
  ('zcms', 'verify_register_when_sign_up', '1', 1);
##ZCMS##
CREATE TABLE IF NOT EXISTS core_contacts (
  contact_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  full_name  VARCHAR(64)            NOT NULL,
  email      VARCHAR(50)            NOT NULL,
  phone      VARCHAR(20)            NOT NULL,
  message    TEXT                   NOT NULL,
  status     INTEGER                NOT NULL,
  created_at DATETIME,
  created_by INTEGER,
  updated_at DATETIME,
  updated_by INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_email_templates (
  email_template_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  template_code     VARCHAR(255),
  module            VARCHAR(40),
  title             VARCHAR(255),
  subject           VARCHAR(255),
  params            TEXT,
  body              TEXT,
  thumb             VARCHAR(255),
  is_core           SMALLINT,
  default_body      TEXT,
  description       TEXT,
  published         SMALLINT,
  created_at        DATETIME,
  created_by        INTEGER,
  updated_at        DATETIME,
  updated_by        INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_emails (
  email_id         INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  subject          VARCHAR(255),
  body             TEXT,
  email_to         TEXT,
  email_cc         TEXT,
  email_bcc        TEXT,
  email_reply_to   TEXT,
  attachment_files TEXT,
  send_status      BOOLEAN,
  send_count       INTEGER DEFAULT 0,
  exec_log         TEXT,
  create_at        DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_languages (
  language_id   INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  language_code CHARACTER(5)           NOT NULL,
  title         VARCHAR(50)            NOT NULL,
  icon          VARCHAR(255),
  is_default    INTEGER                NOT NULL DEFAULT 0,
  image         VARCHAR(50),
  description   VARCHAR(512),
  metakey       TEXT,
  metadesc      TEXT,
  sitename      VARCHAR(1024),
  published     INTEGER                         DEFAULT 0,
  direction     VARCHAR(10)                     DEFAULT 'ltr',
  created_at    DATETIME,
  created_by    INTEGER,
  updated_at    DATETIME,
  updated_by    INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_logs (
  log_id      BIGINT AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  log_module  VARCHAR(100)              NOT NULL,
  log_content TEXT,
  status      VARCHAR(20),
  created_at  DATETIME,
  created_by  INTEGER,
  updated_at  DATETIME,
  updated_by  INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_php_logs (
  log_id           BIGINT AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  log_key          VARCHAR(32),
  message          TEXT,
  file             TEXT,
  line             TEXT,
  type             TEXT,
  status           SMALLINT,
  created_at       DATETIME,
  updated_at       DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_media (
  media_id    INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  title       VARCHAR(255),
  alt_text    VARCHAR(255),
  caption     VARCHAR(255),
  size        DOUBLE PRECISION,
  description TEXT,
  mime_type   VARCHAR(100),
  file_name   VARCHAR(255),
  information TEXT,
  created_at  DATETIME,
  created_by  INTEGER,
  updated_at  DATETIME,
  updated_by  INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_modules (
  module_id   INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  base_name   VARCHAR(40)            NOT NULL,
  name        VARCHAR(255)           NOT NULL,
  location    VARCHAR(40)            NOT NULL,
  description VARCHAR(255),
  class_name  VARCHAR(255)           NOT NULL,
  path        VARCHAR(255)           NOT NULL,
  menu        TEXT,
  router      TEXT,
  version     VARCHAR(20),
  author      VARCHAR(40),
  authoruri   VARCHAR(255),
  uri         VARCHAR(255),
  published   SMALLINT,
  is_core     SMALLINT               NOT NULL,
  ordering    INTEGER,
  created_at  DATETIME,
  created_by  INTEGER,
  updated_at  DATETIME,
  updated_by  INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
INSERT INTO core_modules (base_name, name, location, description, class_name, path, menu, router, version, author, authoruri, uri, published, is_core, ordering, created_at, created_by, updated_at, updated_by) VALUES
('admin', 'm_admin_admin', 'backend', 'm_admin_admin_desc', 'ZCMS\\Backend\\Admin\\Module', '/backend/admin/Module.php', 'a:8:{s:4:"link";s:7:"/admin/";s:4:"rule";s:5:"admin";s:10:"icon_class";s:15:"fa fa-dashboard";s:9:"menu_name";s:13:"m_admin_admin";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:5:"items";a:0:{}s:6:"module";s:5:"admin";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 1, 1, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('user', 'm_admin_user', 'backend', 'm_admin_user_desc', 'ZCMS\\Backend\\User\\Module', '/backend/user/Module.php', 'a:8:{s:4:"rule";s:18:"user|profile|index";s:10:"icon_class";s:10:"fa fa-user";s:9:"menu_name";s:26:"m_admin_user_profile_index";s:4:"link";s:26:"/admin/user/profile/index/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:5:"items";a:0:{}s:6:"module";s:4:"user";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 1, 2, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('template', 'm_admin_template', 'backend', 'm_admin_template_desc', 'ZCMS\\Backend\\Template\\Module', '/backend/template/Module.php', 'a:8:{s:10:"icon_class";s:10:"fa fa-leaf";s:5:"items";a:3:{i:0;a:6:{s:4:"rule";s:14:"template|index";s:10:"icon_class";s:20:"fa fa-file-picture-o";s:9:"menu_name";s:22:"m_admin_template_index";s:4:"link";s:22:"/admin/template/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:16:"template|sidebar";s:10:"icon_class";s:12:"fa fa-trello";s:9:"menu_name";s:24:"m_admin_template_sidebar";s:4:"link";s:24:"/admin/template/sidebar/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:2;a:6:{s:4:"rule";s:15:"template|widget";s:10:"icon_class";s:8:"fa fa-th";s:9:"menu_name";s:23:"m_admin_template_widget";s:4:"link";s:23:"/admin/template/widget/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:8:"template";s:9:"menu_name";s:16:"m_admin_template";s:4:"link";s:16:"/admin/template/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:8:"template";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 1, 3, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('system', 'm_admin_system', 'backend', 'm_admin_system_desc', 'ZCMS\\Backend\\system\\Module', '/backend/system/Module.php', 'a:8:{s:10:"icon_class";s:10:"fa fa-gear";s:5:"items";a:4:{i:0;a:6:{s:4:"rule";s:11:"system|role";s:10:"icon_class";s:8:"fa fa-ra";s:9:"menu_name";s:19:"m_admin_system_role";s:4:"link";s:19:"/admin/system/role/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:11:"system|user";s:10:"icon_class";s:11:"fa fa-users";s:9:"menu_name";s:19:"m_admin_system_user";s:4:"link";s:19:"/admin/system/user/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:2;a:6:{s:4:"rule";s:15:"system|language";s:10:"icon_class";s:14:"fa fa-language";s:9:"menu_name";s:23:"m_admin_system_language";s:4:"link";s:23:"/admin/system/language/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:3;a:6:{s:4:"rule";s:19:"system|module|index";s:10:"icon_class";s:11:"fa fa-cubes";s:9:"menu_name";s:27:"m_admin_system_module_index";s:4:"link";s:27:"/admin/system/module/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:6:"system";s:9:"menu_name";s:14:"m_admin_system";s:4:"link";s:14:"/admin/system/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:6:"system";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 1, 4, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('menu', 'm_admin_menu', 'backend', 'm_admin_menu_desc', 'ZCMS\\Backend\\Menu\\Module', '/backend/menu/Module.php', 'a:8:{s:10:"icon_class";s:13:"fa fa-list-ul";s:5:"items";a:2:{i:0;a:6:{s:4:"rule";s:10:"menu|index";s:10:"icon_class";s:11:"fa fa-slack";s:9:"menu_name";s:18:"m_admin_menu_index";s:4:"link";s:18:"/admin/menu/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:13:"menu|menuitem";s:10:"icon_class";s:10:"fa fa-link";s:9:"menu_name";s:21:"m_admin_menu_menuitem";s:4:"link";s:21:"/admin/menu/menuitem/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:4:"menu";s:9:"menu_name";s:12:"m_admin_menu";s:4:"link";s:12:"/admin/menu/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:4:"menu";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 0, 6, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('slide', 'm_admin_slide', 'backend', 'm_admin_slide_desc', 'ZCMS\\Backend\\Slide\\Module', '/backend/slide/Module.php', 'a:8:{s:10:"icon_class";s:16:"fa fa-area-chart";s:5:"items";a:2:{i:0;a:6:{s:4:"rule";s:11:"slide|index";s:10:"icon_class";s:14:"fa fa-list-alt";s:9:"menu_name";s:19:"m_admin_slide_index";s:4:"link";s:19:"/admin/slide/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:15:"slide|index|new";s:10:"icon_class";s:24:"glyphicon glyphicon-plus";s:9:"menu_name";s:23:"m_admin_slide_index_new";s:4:"link";s:23:"/admin/slide/index/new/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:5:"slide";s:9:"menu_name";s:13:"m_admin_slide";s:4:"link";s:13:"/admin/slide/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:5:"slide";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 0, 8, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('index', 'm_frontend_index', 'frontend', 'm_frontend_index_desc', 'ZCMS\\Frontend\\Index\\Module', '/frontend/index/Module.php', '', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 1, 7, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('auth', 'm_frontend_auth', 'frontend', 'm_frontend_auth_desc', 'ZCMS\\Frontend\\Auth\\Module', '/frontend/auth/Module.php', '', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 0, 9, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('content', 'm_admin_content', 'backend', 'm_admin_content_desc', 'ZCMS\\Backend\\Content\\Module', '/backend/content/Module.php', 'a:8:{s:10:"icon_class";s:12:"fa fa-pencil";s:5:"items";a:3:{i:0;a:6:{s:4:"rule";s:13:"content|posts";s:10:"icon_class";s:17:"fa fa-file-text-o";s:9:"menu_name";s:21:"m_admin_content_posts";s:4:"link";s:21:"/admin/content/posts/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:17:"content|posts|new";s:10:"icon_class";s:24:"glyphicon glyphicon-plus";s:9:"menu_name";s:25:"m_admin_content_posts_new";s:4:"link";s:25:"/admin/content/posts/new/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:2;a:6:{s:4:"rule";s:18:"content|categories";s:10:"icon_class";s:12:"fa fa-folder";s:9:"menu_name";s:26:"m_admin_content_categories";s:4:"link";s:26:"/admin/content/categories/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:7:"content";s:9:"menu_name";s:15:"m_admin_content";s:4:"link";s:15:"/admin/content/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:7:"content";}', null, '0.0.1', 'ZCMS Team', 'http://zcms.com', 'http://www.zcms.com', 1, 0, 5, '2015-12-12 20:15:20', 1, '2015-08-19 15:49:40', 1),
('media', 'm_admin_media', 'backend', 'm_admin_media_desc', 'ZCMS\\Backend\\Media\\Module', '/backend/media/Module.php', 'a:8:{s:10:"icon_class";s:15:"fa fa-picture-o";s:5:"items";a:2:{i:0;a:6:{s:4:"rule";s:13:"media|manager";s:10:"icon_class";s:16:"fa fa-university";s:9:"menu_name";s:21:"m_admin_media_manager";s:4:"link";s:21:"/admin/media/manager/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:17:"media|manager|new";s:10:"icon_class";s:24:"glyphicon glyphicon-plus";s:9:"menu_name";s:25:"m_admin_media_manager_new";s:4:"link";s:25:"/admin/media/manager/new/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:5:"media";s:9:"menu_name";s:13:"m_admin_media";s:4:"link";s:13:"/admin/media/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:5:"media";}', null, '0.0.1', 'ZCMS Team', null, 'http://www.zcms.com', 1, 0, 10, '2015-08-11 18:36:33', 1, '2015-08-19 15:49:40', 1);
##ZCMS##
CREATE TABLE IF NOT EXISTS core_sidebars (
  sidebar_base_name VARCHAR(80) PRIMARY KEY NOT NULL,
  theme_name        VARCHAR(80)             NOT NULL,
  sidebar_name      VARCHAR(255)            NOT NULL,
  ordering          INTEGER                 NOT NULL,
  published         SMALLINT                NOT NULL,
  location          VARCHAR(40)             NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_templates (
  template_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  base_name   VARCHAR(40)            NOT NULL,
  name        VARCHAR(255)           NOT NULL,
  location    VARCHAR(40)            NOT NULL,
  uri         VARCHAR(255),
  description TEXT,
  author      VARCHAR(40),
  authoruri   VARCHAR(255),
  tag         VARCHAR(255),
  version     VARCHAR(20)            NOT NULL,
  published   SMALLINT               NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
INSERT INTO `core_templates` (`base_name`, `name`, `location`, `uri`, `description`, `author`, `authoruri`, `tag`, `version`, `published`) VALUES
  ('default', 't_template_backend_name_default', 'backend', 'http://www.zcms.com', 't_template_backend_name_default_desc', 'ZCMS Team', NULL, 'red, e-commerce, zcms team', '1.0.0', 1),
  ('default', 't_template_frontend_name_default', 'frontend', 'http://www.zcms.com', 't_template_frontend_name_default_desc', 'ZCMS Team', NULL, 'red, zcms team', '1.0.0', 1);
##ZCMS##
CREATE TABLE IF NOT EXISTS core_widget_values (
  widget_value_id   INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  sidebar_base_name VARCHAR(40)            NOT NULL,
  theme_name        VARCHAR(40)            NOT NULL,
  class_name        VARCHAR(255)           NOT NULL,
  title             VARCHAR(255)           NOT NULL,
  options           TEXT                   NOT NULL,
  ordering          INTEGER                NOT NULL,
  published         SMALLINT,
  created_at        DATETIME,
  created_by        INTEGER,
  updated_at        DATETIME,
  updated_by        INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS core_widgets (
  widget_id   INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  base_name   VARCHAR(40)            NOT NULL,
  location    VARCHAR(40)            NOT NULL,
  title       VARCHAR(255)           NOT NULL,
  description VARCHAR(255),
  uri         VARCHAR(255),
  author      VARCHAR(40),
  authoruri   VARCHAR(255),
  version     VARCHAR(20),
  published   SMALLINT,
  is_core     SMALLINT               NOT NULL,
  created_at  DATETIME,
  created_by  INTEGER,
  updated_at  DATETIME,
  updated_by  INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS countries (
  country_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name       VARCHAR(100),
  published  SMALLINT,
  ordering   INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS country_states (
  country_state_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  country_id       INTEGER                NOT NULL,
  name             VARCHAR(255)           NOT NULL,
  short_name       VARCHAR(100),
  alias            VARCHAR(100),
  is_all           INTEGER                NOT NULL DEFAULT 0,
  published        SMALLINT,
  ordering         INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS menu_details (
  menu_detail_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  menu_type_id   INTEGER,
  menu_item_id   INTEGER,
  parent_id      INTEGER,
  ordering       INTEGER,
  published      SMALLINT,
  created_at     DATETIME,
  created_by     INTEGER,
  updated_at     DATETIME,
  updated_by     INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS menu_items (
  menu_item_id  INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name          VARCHAR(255),
  link          VARCHAR(500),
  full_link     VARCHAR(500),
  image         VARCHAR(255),
  thumbnail     VARCHAR(255),
  parent        INTEGER,
  published     SMALLINT,
  require_login SMALLINT               NOT NULL DEFAULT 0,
  class         VARCHAR(255),
  icon          VARCHAR(255),
  created_at    DATETIME,
  created_by    INTEGER,
  updated_at    DATETIME,
  updated_by    INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS menu_types (
  menu_type_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name         VARCHAR(255),
  description  VARCHAR(255),
  published    SMALLINT               NOT NULL,
  created_at   DATETIME,
  created_by   INTEGER,
  updated_at   DATETIME,
  updated_by   INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS slide_show_items (
  slide_show_item_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  title              VARCHAR(255),
  description        TEXT,
  image              VARCHAR(255),
  link               TEXT,
  target             VARCHAR(255),
  slide_show_id      INTEGER,
  ordering           INTEGER,
  published          SMALLINT,
  created_at         DATETIME,
  created_by         INTEGER,
  updated_at         DATETIME,
  updated_by         INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS slide_shows (
  slide_show_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  title         VARCHAR(255),
  alias         VARCHAR(255),
  description   TEXT,
  image         VARCHAR(255),
  published     SMALLINT,
  created_at    DATETIME,
  created_by    INTEGER,
  updated_at    DATETIME,
  updated_by    INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS user_addresses (
  address_id      INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  user_id         INTEGER                NOT NULL,
  first_name      VARCHAR(64)            NOT NULL,
  last_name       VARCHAR(64)            NOT NULL,
  zip_postal_code VARCHAR(10),
  telephone       VARCHAR(20),
  cellphone       VARCHAR(20),
  fax             VARCHAR(20),
  address1        VARCHAR(150)           NOT NULL,
  address2        VARCHAR(150),
  state           VARCHAR(50),
  city            VARCHAR(150)           NOT NULL,
  country         INTEGER                NOT NULL,
  created_at      DATETIME,
  created_by      INTEGER,
  updated_at      DATETIME,
  updated_by      INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS user_role_mapping (
  role_mapping_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  role_id         INTEGER                NOT NULL,
  rule_id         INTEGER                NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS user_roles (
  role_id        INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  name           VARCHAR(64)            NOT NULL,
  is_super_admin SMALLINT               NOT NULL,
  menu           TEXT,
  location       INTEGER                         DEFAULT 0,
  is_default     INTEGER                NOT NULL DEFAULT 0,
  acl            TEXT,
  created_at     DATETIME,
  created_by     INTEGER,
  updated_at     DATETIME,
  updated_by     INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
INSERT INTO user_roles (name, is_super_admin, menu, location, is_default, acl, created_at, created_by, updated_at, updated_by) VALUES
  ('Supper Administrator', 1, 'a:8:{i:0;a:8:{s:4:"link";s:7:"/admin/";s:4:"rule";s:5:"admin";s:10:"icon_class";s:15:"fa fa-dashboard";s:9:"menu_name";s:13:"m_admin_admin";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:5:"items";a:0:{}s:6:"module";s:5:"admin";}i:1;a:8:{s:4:"rule";s:18:"user|profile|index";s:10:"icon_class";s:10:"fa fa-user";s:9:"menu_name";s:26:"m_admin_user_profile_index";s:4:"link";s:26:"/admin/user/profile/index/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:5:"items";a:0:{}s:6:"module";s:4:"user";}i:2;a:8:{s:10:"icon_class";s:12:"fa fa-pencil";s:5:"items";a:3:{i:0;a:6:{s:4:"rule";s:13:"content|posts";s:10:"icon_class";s:17:"fa fa-file-text-o";s:9:"menu_name";s:21:"m_admin_content_posts";s:4:"link";s:21:"/admin/content/posts/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:17:"content|posts|new";s:10:"icon_class";s:24:"glyphicon glyphicon-plus";s:9:"menu_name";s:25:"m_admin_content_posts_new";s:4:"link";s:25:"/admin/content/posts/new/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:2;a:6:{s:4:"rule";s:18:"content|categories";s:10:"icon_class";s:12:"fa fa-folder";s:9:"menu_name";s:26:"m_admin_content_categories";s:4:"link";s:26:"/admin/content/categories/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:7:"content";s:9:"menu_name";s:15:"m_admin_content";s:4:"link";s:15:"/admin/content/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:7:"content";}i:3;a:8:{s:10:"icon_class";s:13:"fa fa-list-ul";s:5:"items";a:2:{i:0;a:6:{s:4:"rule";s:10:"menu|index";s:10:"icon_class";s:11:"fa fa-slack";s:9:"menu_name";s:18:"m_admin_menu_index";s:4:"link";s:18:"/admin/menu/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:13:"menu|menuitem";s:10:"icon_class";s:10:"fa fa-link";s:9:"menu_name";s:21:"m_admin_menu_menuitem";s:4:"link";s:21:"/admin/menu/menuitem/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:4:"menu";s:9:"menu_name";s:12:"m_admin_menu";s:4:"link";s:12:"/admin/menu/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:4:"menu";}i:4;a:8:{s:10:"icon_class";s:16:"fa fa-area-chart";s:5:"items";a:2:{i:0;a:6:{s:4:"rule";s:11:"slide|index";s:10:"icon_class";s:14:"fa fa-list-alt";s:9:"menu_name";s:19:"m_admin_slide_index";s:4:"link";s:19:"/admin/slide/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:15:"slide|index|new";s:10:"icon_class";s:24:"glyphicon glyphicon-plus";s:9:"menu_name";s:23:"m_admin_slide_index_new";s:4:"link";s:23:"/admin/slide/index/new/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:5:"slide";s:9:"menu_name";s:13:"m_admin_slide";s:4:"link";s:13:"/admin/slide/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:5:"slide";}i:5;a:8:{s:10:"icon_class";s:15:"fa fa-picture-o";s:5:"items";a:2:{i:0;a:6:{s:4:"rule";s:13:"media|manager";s:10:"icon_class";s:16:"fa fa-university";s:9:"menu_name";s:21:"m_admin_media_manager";s:4:"link";s:21:"/admin/media/manager/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:17:"media|manager|new";s:10:"icon_class";s:24:"glyphicon glyphicon-plus";s:9:"menu_name";s:25:"m_admin_media_manager_new";s:4:"link";s:25:"/admin/media/manager/new/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:5:"media";s:9:"menu_name";s:13:"m_admin_media";s:4:"link";s:13:"/admin/media/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:5:"media";}i:6;a:8:{s:10:"icon_class";s:10:"fa fa-leaf";s:5:"items";a:3:{i:0;a:6:{s:4:"rule";s:14:"template|index";s:10:"icon_class";s:20:"fa fa-file-picture-o";s:9:"menu_name";s:22:"m_admin_template_index";s:4:"link";s:22:"/admin/template/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:16:"template|sidebar";s:10:"icon_class";s:12:"fa fa-trello";s:9:"menu_name";s:24:"m_admin_template_sidebar";s:4:"link";s:24:"/admin/template/sidebar/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:2;a:6:{s:4:"rule";s:15:"template|widget";s:10:"icon_class";s:8:"fa fa-th";s:9:"menu_name";s:23:"m_admin_template_widget";s:4:"link";s:23:"/admin/template/widget/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:8:"template";s:9:"menu_name";s:16:"m_admin_template";s:4:"link";s:16:"/admin/template/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:8:"template";}i:7;a:8:{s:10:"icon_class";s:10:"fa fa-gear";s:5:"items";a:4:{i:0;a:6:{s:4:"rule";s:11:"system|role";s:10:"icon_class";s:8:"fa fa-ra";s:9:"menu_name";s:19:"m_admin_system_role";s:4:"link";s:19:"/admin/system/role/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:1;a:6:{s:4:"rule";s:11:"system|user";s:10:"icon_class";s:11:"fa fa-users";s:9:"menu_name";s:19:"m_admin_system_user";s:4:"link";s:19:"/admin/system/user/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:2;a:6:{s:4:"rule";s:15:"system|language";s:10:"icon_class";s:14:"fa fa-language";s:9:"menu_name";s:23:"m_admin_system_language";s:4:"link";s:23:"/admin/system/language/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}i:3;a:6:{s:4:"rule";s:19:"system|module|index";s:10:"icon_class";s:11:"fa fa-cubes";s:9:"menu_name";s:27:"m_admin_system_module_index";s:4:"link";s:27:"/admin/system/module/index/";s:11:"link_target";s:0:"";s:10:"link_class";s:0:"";}}s:4:"rule";s:6:"system";s:9:"menu_name";s:14:"m_admin_system";s:4:"link";s:14:"/admin/system/";s:10:"link_class";s:0:"";s:11:"link_target";s:0:"";s:6:"module";s:6:"system";}}', 1, 0, '{"rules":[],"links":[]}', null, 1, '2015-08-29 10:46:38', 1);
##ZCMS##
CREATE TABLE IF NOT EXISTS user_rules (
  rule_id         INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  module          VARCHAR(64)            NOT NULL,
  module_name     VARCHAR(64)            NOT NULL,
  controller      VARCHAR(64)            NOT NULL,
  controller_name VARCHAR(64)            NOT NULL,
  action          VARCHAR(64)            NOT NULL,
  action_name     VARCHAR(64)            NOT NULL,
  sub_action      TEXT,
  mca             VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS user_subscribes (
  user_subscribe_id INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  email             VARCHAR(255),
  first_name        VARCHAR(255),
  last_name         VARCHAR(255),
  is_subscribe      BOOLEAN,
  subscribe_type    VARCHAR(20),
  created_at        DATETIME,
  created_by        INTEGER,
  updated_at        DATETIME,
  updated_by        INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS categories
(
  category_id   INTEGER UNSIGNED NOT NULL  PRIMARY KEY AUTO_INCREMENT,
  root          INTEGER UNSIGNED NOT NULL,
  lft           INTEGER UNSIGNED NOT NULL,
  rgt           INTEGER UNSIGNED NOT NULL,
  level         INTEGER UNSIGNED NOT NULL,
  module        VARCHAR(64),
  title         VARCHAR(255)     NOT NULL,
  alias         VARCHAR(255)     NOT NULL UNIQUE,
  thumb         TEXT,
  image         TEXT,
  hits          INTEGER UNSIGNED,
  published     SMALLINT,
  description   TEXT,
  meta_desc     VARCHAR(255),
  meta_keywords VARCHAR(255),
  metadata      VARCHAR(255),
  options       LONGTEXT,
  created_at    DATETIME,
  created_by    INTEGER UNSIGNED,
  updated_at    DATETIME,
  updated_by    INTEGER UNSIGNED
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
##ZCMS##
INSERT INTO categories (category_id, root, module, lft, rgt, level, title, alias, image, hits, published, description, meta_desc, meta_keywords, metadata, options, created_at, created_by, updated_at, updated_by) VALUES (1, 1, 'content', 1, 8, 1, 'Root', 'root', null, null, 1, null, null, null, null, null, '2015-12-12 20:15:20', 1, '2015-12-12 20:15:20', 1);
##ZCMS##
CREATE TABLE IF NOT EXISTS posts
(
  post_id        INTEGER UNSIGNED NOT NULL  PRIMARY KEY AUTO_INCREMENT,
  category_id    INTEGER DEFAULT NULL,
  post_parent    INTEGER UNSIGNED,
  post_type      VARCHAR(20), # post revision
  title          VARCHAR(255)     NOT NULL,
  alias          VARCHAR(255)     NOT NULL,
  image          TEXT,
  hits           INTEGER UNSIGNED,
  tags           TEXT,
  version        INTEGER UNSIGNED,
  published      SMALLINT,
  published_at   DATETIME,
  intro_text     TEXT,
  full_text      LONGTEXT,
  meta_desc      VARCHAR(255),
  meta_key       VARCHAR(255),
  metadata       VARCHAR(255),
  options        LONGTEXT,
  comment_count  INTEGER,
  comment_status VARCHAR(20),
  created_at     DATETIME,
  created_by     INTEGER,
  updated_at     DATETIME,
  updated_by     INTEGER
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS medias
(
  media_id    INTEGER UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
  title       VARCHAR(255),
  alt_text    VARCHAR(255),
  caption     VARCHAR(255),
  description TEXT,
  mime_type   VARCHAR(100),
  file_name   VARCHAR(255),
  information TEXT,
  created_at  DATETIME,
  created_by  INTEGER,
  updated_at  DATETIME,
  updated_by  INTEGER
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
##ZCMS##
CREATE TABLE IF NOT EXISTS users (
  user_id                 INTEGER AUTO_INCREMENT PRIMARY KEY     NOT NULL,
  role_id                 INTEGER                NOT NULL,
  first_name              VARCHAR(32)            NOT NULL,
  last_name               VARCHAR(32)            NOT NULL,
  display_name            VARCHAR(255),
  email                   VARCHAR(128)           NOT NULL,
  password                VARCHAR(255),
  salt                    VARCHAR(255),
  avatar                  VARCHAR(255),
  facebook_id             BIGINT,
  is_active               SMALLINT               NOT NULL,
  is_active_facebook      SMALLINT DEFAULT 0,
  is_active_google        SMALLINT DEFAULT 0,
  language_code           VARCHAR(5),
  reset_password_token    TEXT,
  reset_password_token_at DATETIME,
  active_account_at       DATETIME,
  active_account_token    TEXT,
  active_account_type     VARCHAR(32),
  coin                    DOUBLE PRECISION DEFAULT 0,
  token                   VARCHAR(255),
  gender                  SMALLINT,
  mobile                  VARCHAR(20),
  birthday                DATE,
  default_bill_address    INTEGER,
  default_ship_address    INTEGER,
  default_payment         INTEGER,
  country_id              INTEGER,
  country_state_id        INTEGER,
  short_description       VARCHAR(255),
  created_at              DATETIME,
  created_by              INTEGER,
  updated_at              DATETIME,
  updated_by              INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;