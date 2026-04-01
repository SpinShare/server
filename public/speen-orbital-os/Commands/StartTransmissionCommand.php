<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class StartTransmissionCommand extends Command
{
    public function getName()
    {
        return "start_transmission";
    }

    public function getDescription()
    {
        return "Starts the transmission of orbital data";
    }

    public function getUsage()
    {
        return "start_transmission";
    }

    public function isAuthorizedOnly()
    {
        return true;
    }

    public function execute(array $args, bool $isAuthorized)
    {
        $baseUrl = "https://spinsha.re/api/temp/start_transmission";
        $token = base64_encode(date('H'));
        
        return $baseUrl . "?token=" . $token;
    }
}