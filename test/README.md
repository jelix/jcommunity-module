This is an application to test the module.

A docker configuration is provided to launch the application into a container.

Before launching containers, you have to run this command:

```
./run-docker build
```

To launch containers, just run `./run-docker`.

The first time you run the containers, you have to initialize the database and
application configuration by executing these commands:

```
./app-ctl reset
```

Then open your browser and go at http://localhost:8024/ .


You can execute some commands into the php container, by using this command:

```
./app-ctl <command>
```

Available commands:

* `reset`: to reinitialize the application with postgresql 
* `reset-mysql`: to reinitialize the application with mysql 
* `reset-sqlite`: to reinitialize the application with sqlite 
* `composer-update` and `composer-install`: to update PHP packages 
* `clean-tmp`: to delete temp files 
* `install`: to launch the Jelix installer
* `psql`: to enter into the psql cli
* `mysql`: to enter into the mysql cli 


You can also connect to the postgresql server and mysql server, on the port
respectively 8548 and 8549.

You can change port by setting some environment variables. Examples:

```
export JCOMMUNITY_WEB_PORT=8085
export JCOMMUNITY_MYSQL_PORT=3307
export JCOMMUNITY_PGSQL_PORT=5433

./run-docker

```
