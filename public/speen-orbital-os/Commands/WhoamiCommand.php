<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class WhoamiCommand extends Command
{
    public function getName()
    {
        return "whoami";
    }

    public function getDescription()
    {
        return "Display the current user";
    }

    public function getUsage()
    {
        return "whoami";
    }

    public function execute(array $args, bool $isAuthorized)
    {
        if ($isAuthorized) {
            return "You are §54Fstar_gazer§o.";
        } else {
            return "I don't know.";
        }
    }
}
