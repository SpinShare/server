<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class CommandFactory
{
    /** @var Command[] */
    private $commands = [];

    public function __construct()
    {
        $this->register(new HelpCommand($this));
        $this->register(new LoveCommand());
        $this->register(new ArchiveCommand());
        $this->register(new LoginCommand());
        $this->register(new WhoamiCommand());
        $this->register(new StartTransmissionCommand());
    }

    private function register(Command $command)
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * @param string $name
     * @return Command|null
     */
    public function getCommand(string $name)
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * @param bool $isAuthorized
     * @return Command[]
     */
    public function getAvailableCommands(bool $isAuthorized)
    {
        return array_filter($this->commands, function (Command $c) use ($isAuthorized) {
            return !$c->isAuthorizedOnly() || $isAuthorized;
        });
    }
}
