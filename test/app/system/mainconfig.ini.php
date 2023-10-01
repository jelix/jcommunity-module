;<?php die(''); ?>
;for security reasons , don't remove or modify the first line

locale=fr_FR
charset=UTF-8

availableLocales=fr_FR

; see http://www.php.net/manual/en/timezones.php for supported values
timeZone="Europe/Paris"

theme=default

[modules]
jelix.enabled=on
jelix.installparam[wwwfiles]=vhost

test.enabled=on
jcommunity.enabled=on

jcommunity.installparam[manualconfig]=on
jcommunity.installparam[eps]="[index,admin]"
jcommunity.installparam[defaultuser]=on

jmessenger.enabled=on

jacl2.enabled=on
jacl2.installparam[eps]="[index,admin]"

jacl2db.enabled=on
jacl2db.installparam[defaultuser]=on
jacl2db.installparam[defaultgroups]=on

jacldb.enabled=off
jauth.enabled=off
jauthdb.enabled=off
jpref.enabled=off

jauthdb_admin.enabled=on
master_admin.enabled=on
jacl2db_admin.enabled=on

[coordplugins]
auth=auth.coord.ini.php

[responses]
html=testResponse


[error_handling]
messageLogFormat="%date%\t%ip%\t[%code%]\t%msg%\n\tat: %file%\t%line%\n\turl: %url%\n\t%http_method%: %params%\n\treferer: %referer%\n%trace%\n\n"
errorMessage="Une erreur technique est survenue. Désolé pour ce désagrément."


[compilation]
checkCacheFiletime=on
force=off

[jResponseHtml]
plugins=debugbar

[urlengine]

; enable the parsing of the url. Set it to off if the url is already parsed by another program
; (like mod_rewrite in apache), if the rewrite of the url corresponds to a simple url, and if
; you use the significant engine. If you use the simple url engine, you can set to off.
enableParser=on

multiview=off

; basePath corresponds to the path to the base directory of your application.
; so if the url to access to your application is http://foo.com/aaa/bbb/www/index.php, you should
; set basePath = "/aaa/bbb/www/".
; if it is http://foo.com/index.php, set basePath="/"
; Jelix can guess the basePath, so you can keep basePath empty. But in the case where there are some
; entry points which are not in the same directory (ex: you have two entry point : http://foo.com/aaa/index.php
; and http://foo.com/aaa/bbb/other.php ), you MUST set the basePath (ex here, the higher entry point is index.php so
; : basePath="/aaa/" )
basePath=/

; leave empty to have jelix error messages
notfoundAct=
;notfoundAct = "jelix~error:notfound"

jelixWWWPath="jelix/"
[fileLogger]
default=messages.log


[mailer]
webmasterEmail="root@localhost"
webmasterName=Root
mailerType=file


[mailLogger]
email="root@localhost"

[jcommunity]
loginResponse=html
verifyNickname=on
passwordChangeEnabled=on
accountDestroyEnabled=on
useJAuthDbAdminRights=on
registrationEnabled=on
resetPasswordEnabled=on
resetAdminPasswordEnabled=on
noRedirectionOnAuthenticatedLoginPage=off
disableJPref=on
publicProperties[]=login
publicProperties[]=nickname
publicProperties[]=create_date
;validationKeyTTL=1560
notifyAccountChange=off
notificationReceiverEmail=
notificationReceiverName=

[acl2]
driver=db
hiddenRights=
hideRights=false
authAdapterClass=jAcl2JAuthAdapter

[webassets_common]
master_admin.css[]="$jelix/design/master_admin.css"

jacl2_admin.css[]="$jelix/design/jacl2.css"
jacl2_admin.js[]="$jelix/js/jacl2db_admin.js"
jacl2_admin.require=jquery_ui

[session]
storage=
