<?php

namespace Nedbase\Composer\Report;

use Symfony\Component\Console\Output\OutputInterface;

interface ReportInterface
{
    /**
     * @param array{advisories?: array<string, array<array{cve: string, title: string, link: string, severity?: string, affectedVersions: string, reportedAt: string}>>, abandoned?: array<string, string|null>} $source
     * @param OutputInterface $output
     * @return void
     */
    public function generate(array $source, OutputInterface $output): void;
}
