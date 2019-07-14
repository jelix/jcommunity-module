#!/bin/sh
APPDIR="/jelixapp/test"

if [ ! -d $APPDIR/var/log ]; then
    mkdir $APPDIR/var/log
fi

if [ ! -d $APPDIR/temp/ ]; then
    mkdir $APPDIR/temp/
else
    rm -rf $APPDIR/temp/*
fi
touch $APPDIR/temp/.dummy
