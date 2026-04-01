<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class LoveCommand extends Command
{
    public function getName()
    {
        return "love";
    }

    public function getDescription()
    {
        return "Express your love";
    }

    public function getUsage()
    {
        return "love [your name]";
    }

    public function isAuthorizedOnly()
    {
        return true;
    }

    public function execute(array $args, bool $isAuthorized)
    {
        if (count($args) === 0) {
            return "Usage: " . $this->getUsage();
        }
        return "§F55I love you too, {$args[0]}!";
    }
}
