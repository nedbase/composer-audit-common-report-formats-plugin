<?php

namespace Nedbase\Composer\Report;

use Symfony\Component\Console\Output\OutputInterface;

interface ReportInterface
{
    public function generate(string $source, OutputInterface $output): void;
}
