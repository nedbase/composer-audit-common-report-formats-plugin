<?php

namespace Nedbase\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class CommonReportFormatsAuditPlugin implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public function getCapabilities(): array
    {
        return [
            CommandProviderCapability::class => CommandProvider::class,
        ];
    }
}
