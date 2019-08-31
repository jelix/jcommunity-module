

Version 1.3
===========
 
- **New features to reset password** from the jauthdb_admin module
   - New `resetAdminPasswordEnabled` configuration parameter.
     It allows to activate the possibility for an admin user to launch a
     process of a password reset of a user, instead of changing directly
     the password.
   - new page for a user to set a new password after the administration has
     resetted his password.
   - new page for the administrator to reset a password of a user
- **new page to resend validation email** (by the administrator)
- **TTL of the validation is configurable**. Registration key is now valid only two days by default.
- **Fix security issue** about the registration key and password retrieval key.
  There were always the same key for a user.

Developers:

- configurator for Jelix 1.7: interactive configuration of parameters defaultuser & defaultusers
- mails contents are moved to locales properties
- new `urls_registration.xml` file to declare registration admin page separately
 from other pages
- Replace Vagrant by Docker for the test app
- **the `login` field is no more the primary key**, as it causes some issue with
  some database. The `id` is now the primary key.
- Fix installer with default user json file

Version 1.2.2
=============

- Use jAuth::canChangePassword() of Jelix 1.6.21
- compatibility with the upcoming Jelix 1.7.0. Update install scripts for 
   Jelix 1.6.19 and 1.7-beta.4


Version 1.2.1
=============

- add locales for PT
- fix regression in the installer
- support of `liveconfig.ini.php` of Jelix 1.6.18+ to store the encryption key
- Fix localized templates: add default templates

Version 1.2.0
=============

- **New process to request a password**. There is not anymore a form in which the
  user has to indicate a key and a login. The email contain a link having the
  login and the key.
- **New process for registration**.
    It follows "modern" processes for the registration:
    - the form contain the login, email but also the password
    - the email indicate a link, which contain the registration key
      so the user do not need anymore to fill a new form
- **User profile: improve the privacy**.
  A configuration property, publicProperties, allows to
  specify which fields are public, so only these fields
  are shown to any visitor.
- **sends emails in HTML** instead of in plain text.
- **New form allowing user to change its password** when he is authenticated
- Account deletion: ask the password account to confirm
- Improvements in some messages and templates

- Possibility to configure an other form instead of account form.
  In the auth.coord.ini, support of a new parameter, `userform`,
  in the `Db` section. It should contains the selector of the account
  form.
- more integration with jauthdb_admin
- New option `useJAuthDbAdminRights` to take care of jauthdb_admin rights
- New option `accountDestroyEnabled` to allow to delete accounts
- Some features are enabled only if email is well configured

- remove deprecated en_EN locales and en_GB locales
- no more templates for each languages.
- improvements into the installer
- nickname field is now optional
 

Version 1.1.1
=============

- jPref is optional
- fix storage of encryption key for persistant cookie
- fix installation to be more indempotent


Version 1.1.0
=============

To use this version, you need to upgrade Jelix to 1.6.5 minimum.

New features and improvements
------------------------------

- Some improvements have been made to use jCommunity with the master_admin module (with Jelix 1.6.5+ only)
- New install parameters:
   - ```masteradmin```: to indicate we want to use jcommunity for authentication system
   - ```notjcommunitytable```: to indicate to not create the community_users table
   - ```migratejauthdbusers```: to migrate users from a standard jlx_user
     table to a community_users table
- new configuration parameters you can set into a ```jcommunity``` section into
  the application configuration
   - ```loginResponse```: the alias of the html response to use to display the
     main login form.
   - ```registrationEnabled```: to disable or enable the registration feature
   - ```resetPasswordEnabled```: to disable or enable the reset password feature
- you can use jPref to enable/disable registration or password reseting.
- Templates: for unknown users, add a link to return to the login form
- removed the deprecated jcommunity_phorum module

Fixed bugs
----------

- Fix infinite loop after a logout in some cases
- Fix auth_url_return generated into the login form


Version 1.0
===========

- same features as 0.2 and 0.3. 
- Compatibility with Jelix 1.4, 1.5, 1.6

