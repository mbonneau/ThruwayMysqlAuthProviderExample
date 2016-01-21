CREATE DATABASE thruway_auth_example;
USE thruway_auth_example;
CREATE TABLE users (
  id MEDIUMINT NOT NULL AUTO_INCREMENT,
  login CHAR(30) NOT NULL,
  password CHAR(30) NOT NULL,
  PRIMARY KEY (id)
);
INSERT INTO users(login, password) VALUES('sally', 'goodpassword');