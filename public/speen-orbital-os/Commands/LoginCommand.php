<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class LoginCommand extends Command
{
    public function getName()
    {
        return "login";
    }

    public function getDescription()
    {
        return "Log in to the system";
    }

    public function getUsage()
    {
        return "login [username] [password]";
    }

    public function execute(array $args, bool $isAuthorized)
    {
        $username = $args[0] ?? null;
        $password = $args[1] ?? null;

        if (!$username || !$password) {
            return "Usage: " . $this->getUsage();
        }

        if ($username === "star_blazer" && $password === "spinsofast") {
            return "§5F5Login successful! Welcome, star_blazer";
        } else {
            return "§F55Login failed: Invalid username or password.";
        }
    }
}
