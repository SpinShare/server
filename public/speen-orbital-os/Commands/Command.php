<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

abstract class Command
{
    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return string
     */
    abstract public function getDescription();

    /**
     * @return string
     */
    abstract public function getUsage();

    /**
     * @param array $args
     * @param bool $isAuthorized
     * @return string
     */
    abstract public function execute(array $args, bool $isAuthorized);

    /**
     * @return bool
     */
    public function isAuthorizedOnly()
    {
        return false;
    }
}
