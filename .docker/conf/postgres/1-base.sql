CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

/* CREATE ROLE bigfish_dba WITH LOGIN ENCRYPTED PASSWORD 'B1gF!s4@'; */


CREATE TABLE IF NOT EXISTS users (
	id serial PRIMARY KEY,
	name TEXT NOT NULL,
	email TEXT NOT NULL,
	dateofbirth DATE NOT NULL,
	isactive boolean NOT NULL DEFAULT false,
	createdat TIMESTAMP NOT NULL,
	updatedat TIMESTAMP NOT NULL,
	deletedat TIMESTAMP DEFAULT NULL
);

CREATE SEQUENCE IF NOT EXISTS users_id_seq OWNED BY users.id;
ALTER TABLE users ALTER id SET DEFAULT nextval('users_id_seq');


CREATE TABLE IF NOT EXISTS user_phone (
	user_id serial NOT NULL,
	phonenumber VARCHAR(20) NOT NULL,
	isdefault boolean NOT NULL DEFAULT false,
	createdat TIMESTAMP NOT NULL,
	updatedat TIMESTAMP NOT NULL
);
