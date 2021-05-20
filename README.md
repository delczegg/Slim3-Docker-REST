Docker Compose configuration to run PHP 7.4 with Nginx, PHP-FPM, PostgreSQL 10.1 and Composer.

## Overview

* web (Nginx)
* php (PHP 7.4 with PHP-FPM)
* db (PostgreSQL 10.1)
* composer


## Dependencies


* [Docker CE](https://docs.docker.com/engine/installation/)
* [Docker Compose](https://docs.docker.com/compose/install)
* Git (not required)



## Use 

Run `docker-compose up`.
Nginx listening on `127.0.0.1:9090` and PostgreSQL mapped on host `127.0.0.1:5433`.


### Composer

`docker-compose run composer <cmd>`

The `cmd` is a legacy composer command.


### PostgreSQL

Default connection:
`docker-compose exec db psql -U postgres`

.env file default parameters:
`docker-compose exec db psql -U dbuser dbname`

Any .sh or .sql file you add in `./.docker/conf/postgres` will be automatically loaded at boot time.
The db name, db user and db password edit the `.env` file at the project's root.


### PHP

`docker-compose exec php php -v`
Change PHP configuration if needed: `.docker/conf/php/php.ini`.



## Use Rest API

### Create User(s)

API URL: http://127.0.0.1:9090/users/create
Method: POST
Sample JSON: ./app/.Samples/user_create.json

Mandatory fields:
	
	- name
	- email 
	- default_phone
	- dateofbirth
	- isactive

### Update User(s)

API URL: http://127.0.0.1:9090/users
Method: PATCH
Sample JSON: ./app/.Samples/user_update.json

Mandatory field:

	- id

Optional (one of):

	- name
	- email 
	- default_phone
	- dateofbirth
	- isactive


### Delete User(s)

API URL: http://127.0.0.1:9090/users
Method: DELETE
Sample JSON: ./app/.Samples/user_delete.json

Mandatory field:

	- id


### List User(s)

API URL: http://127.0.0.1:9090/users[/{id}]
Method: GET, POST
Sample JSON: ./app/.Samples/user_list.json

For GET method, url parameter (id) is optional for specified user data query. 
When POST (sorting parameters), don't use the url parameter.

POST params example:

	- sort: [ "name:desc", "isactive::asc", "email::asc" ], default sorting is "asc".
	- getdeleted: true/false
		- true: get all record (active and deleted both)
		- false: get active records


### Create/Update Phones

** Create
API URL: http://127.0.0.1:9090/phones/create
Method: POST
Sample JSON: ./app/.Samples/phone_create_update.json

** Update
API URL: http://127.0.0.1:9090/phones
Method: PATCH
Sample JSON: ./app/.Samples/phone_create_update.json

Mandatory fields:

	- user_id
	- phonenumber
	- isdefault

### Delete Phones

API URL: http://127.0.0.1:9090/phones
Method: DELETE
Sample JSON: ./app/.Samples/phone_delete.json

Mandatory fields:

	- user_id
	- phonenumber

WARNING: default phonenumber not allowed for delete!



### List Phones

API URL: http://127.0.0.1:9090/phones[/{user_id}]
Method: GET

When declared user_id url parameter, the result contains only for selected user phonenumbers collection.




