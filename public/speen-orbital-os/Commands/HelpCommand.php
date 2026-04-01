<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class HelpCommand extends Command
{
    private $factory;

    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    public function getName()
    {
        return "help";
    }

    public function getDescription()
    {
        return "Show this help message";
    }

    public function getUsage()
    {
        return "help";
    }

    public function execute(array $args, bool $isAuthorized)
    {
        $commands = $this->factory->getAvailableCommands($isAuthorized);

        $lines = [];
        $lines[] = "§fffhelp                        §666Show this help message";
        $lines[] = "§fffclear                       §666Clear the screen";

        foreach ($commands as $command) {
            if ($command->getName() === "help") {
                continue;
            }
            $usage = str_pad($command->getUsage(), 28, " ", STR_PAD_RIGHT);
            $lines[] = "§fff{$usage}§666{$command->getDescription()}";
        }

        return implode("\n", $lines);
    }
}
