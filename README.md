jCommunity is set of modules to add "community" features to a web site made with the [Jelix framework](http://jelix.org).

It provides:

* user registration: account creation, with confirmation by email and activation key. The form contains a captcha.
* users can choose their password
* possibility to ask a new password when the user has lost his password (again, confirmation by email and activation key)
* After a successful login, jcommunity can redirect to the page before the login
* Profile editing
* login/logout form
* many new events in controllers, to allow you to do processing at each step of the registration and other actions, so your own module can verify or do additional things.
* notification messages with jMessage
* a specific form for jauthdb_admin is provided
* optional: authentification status can be synchronized with a phorum installation. (module jcommunity_phorum)


Installation
------------

It works only with Jelix 1.2 and 1.3.

* Extract the content of the downloaded archive.
* Copy the jcommunity directory in a module repository of your application (in
  yourapp/modules for example).
* install the jcommunity module
    * with Jelix 1.2, with the command installmodule in the lib/jelix-scripts/
      directory: php jelix.php --myapp installmodule jcommunity
    * with Jelix 1.3, with the command installmodule in your application: php
      cmd.php installmodule jcommunity
* You can change the start action in index/config.ini.php like this:

      startModule=jcommunity
      startAction="login:index"

In your application, you should **not** use anything from the jauth module, but
only from jcommunity, since it provides all needed things, with some different
behaviors.


Others documentation
--------------------

On [the project web site](https://github.com/laurentj/jcommunity) :


* [Extending jcommunity](https://github.com/laurentj/jcommunity/wiki/extending_jcommunity)
* [How to contribute](https://github.com/laurentj/jcommunity/wiki/contribute)

