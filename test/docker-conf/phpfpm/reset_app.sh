#!/bin/sh
APPDIR="/jelixapp/test"

if [ -f $APPDIR/var/config/CLOSED ]; then
    rm -f $APPDIR/var/config/CLOSED
fi

if [ ! -d $APPDIR/var/log ]; then
    mkdir $APPDIR/var/log
fi

if [ -f $APPDIR/var/config/profiles.ini.php.dist ]; then
    cp $APPDIR/var/config/profiles.ini.php.dist $APPDIR/var/config/profiles.ini.php
fi
if [ -f $APPDIR/var/config/localconfig.ini.php.dist ]; then
    cp $APPDIR/var/config/localconfig.ini.php.dist $APPDIR/var/config/localconfig.ini.php
fi
if [ -f $APPDIR/var/config/installer.ini.php ]; then
    rm -f $APPDIR/var/config/installer.ini.php
fi
if [ -f $APPDIR/var/config/liveconfig.ini.php ]; then
    rm -f $APPDIR/var/config/liveconfig.ini.php
fi
rm -rf $APPDIR/var/log/*
rm -rf $APPDIR/var/db/*
rm -rf $APPDIR/var/mails/*
rm -rf $APPDIR/var/uploads/*
touch $APPDIR/var/log/.dummy
touch $APPDIR/var/db/.dummy
touch $APPDIR/var/mails/.dummy
touch $APPDIR/var/uploads/.dummy

/bin/clean_tmp.sh
/bin/set_rights.sh

php $APPDIR/install/installer.php --verbose



