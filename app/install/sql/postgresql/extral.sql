CREATE TABLE IF NOT EXISTS bug_tracking (
  id                       SERIAL PRIMARY KEY     NOT NULL,
  bug_tracking_priority_id INTEGER,
  bug_tracking_type_id     INTEGER,
  bug_tracking_status_id   INTEGER DEFAULT 1,
  role_id                  INTEGER,
  description              TEXT,
  image                    TEXT,
  fixed_at                 TIMESTAMP,
  fixed_by                 INTEGER,
  created_at               TIMESTAMP,
  created_by               INTEGER,
  updated_at               TIMESTAMP,
  updated_by               INTEGER
);
--ZCMS--
CREATE TABLE IF NOT EXISTS bug_tracking_priority (
  id       SERIAL PRIMARY KEY     NOT NULL,
  name     VARCHAR(255),
  ordering INTEGER
);
--ZCMS--
CREATE TABLE IF NOT EXISTS bug_tracking_status (
  id       SERIAL PRIMARY KEY     NOT NULL,
  name     VARCHAR(255),
  ordering INTEGER
);
--ZCMS--
CREATE TABLE IF NOT EXISTS bug_tracking_type (
  id       SERIAL PRIMARY KEY     NOT NULL,
  name     VARCHAR(255),
  ordering INTEGER
);
--ZCMS--
CREATE TABLE IF NOT EXISTS core_contacts (
  contact_id SERIAL PRIMARY KEY     NOT NULL,
  full_name  VARCHAR(64)            NOT NULL,
  email      VARCHAR(50)            NOT NULL,
  phone      VARCHAR(20)            NOT NULL,
  message    TEXT                   NOT NULL,
  status     INTEGER                NOT NULL,
  created_at TIMESTAMP,
  created_by INTEGER,
  updated_at TIMESTAMP,
  updated_by INTEGER
);
--ZCMS--