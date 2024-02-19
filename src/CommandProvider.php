<?php

namespace Nedbase\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Nedbase\Composer\Command\JUnitCommand;
use Nedbase\Composer\Command\TrivyCommand;

class CommandProvider implements CommandProviderCapability
{

    public function getCommands(): array
    {
        return [
            new JUnitCommand(),
            new TrivyCommand(),
        ];
    }
}
