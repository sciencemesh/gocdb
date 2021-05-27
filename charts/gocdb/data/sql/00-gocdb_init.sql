-- gocdb_init.sql -- Initializes the GOCDB database with necessary startup database
CREATE DATABASE gocdb DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_bin;

-- User creation
CREATE USER '{{ .Values.database.gocdbUser.name }}'@'%' IDENTIFIED BY '{{ .Values.database.gocdbUser.password }}';
GRANT ALL PRIVILEGES ON gocdb.* TO '{{ .Values.database.gocdbUser.name }}'@'%';
FLUSH PRIVILEGES;
