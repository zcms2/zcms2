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