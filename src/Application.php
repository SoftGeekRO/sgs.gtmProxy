<?php

namespace SGS;

use SGS\GTM\GtmProxy;

class App
{
    public function run(): void
    {
        $greeter = new Greeter();
        echo $greeter->sayHello("World") . "\n";
    }
}