<?php

namespace Nedbase\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Nedbase\Composer\Command\JUnitCommand;

class CommandProvider implements CommandProviderCapability
{

    public function getCommands(): array
    {
        return [new JUnitCommand()];
    }
}
