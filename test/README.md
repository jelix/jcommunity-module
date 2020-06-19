This is an application to test the module.

A docker configuration is provided to launch the application into a container.

To launch containers:

```
./run-docker build
./run-docker
```

Then open your browser and go at http://localhost:8024/ .


You can execute some commands into the php container, by using this command:

```
./dockerappctl <command>
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
