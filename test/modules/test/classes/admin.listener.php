<?php

class adminListener extends jEventListener
{
    public function onmasteradminGetInfoBoxContent($event)
    {
        $home = new masterAdminMenuItem('home', "home", jUrl::get('test~default:index'));
        $event->add($home);
    }
}
