#!/bin/sh
APPDIR='/jelixapp/test'

USER="$1"
GROUP="$2"

if [ "$USER" = "" ]; then
    USER="usertest"
fi

if [ "$GROUP" = "" ]; then
    GROUP="grouptest"
fi


DIRS="$APPDIR/var/config $APPDIR/var/db $APPDIR/var/log $APPDIR/var/mails $APPDIR/temp/"

chown -R $USER:$GROUP $DIRS
chmod -R ug+w $DIRS
chmod -R o-w $DIRS
