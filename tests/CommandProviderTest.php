<?php

namespace Nedbase\Test\Composer;

use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Nedbase\Composer\CommandProvider;
use PHPUnit\Framework\TestCase;

class CommandProviderTest extends TestCase
{
    public function testGetCommands(): void
    {
        $provider = new CommandProvider();

        $this->assertInstanceOf(CommandProviderCapability::class, $provider);

        $commands = $provider->getCommands();
        foreach ($commands as $command) {
            $this->assertInstanceOf(BaseCommand::class, $command);
        }
    }
}
