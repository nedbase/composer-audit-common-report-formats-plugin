<?php

namespace Nedbase\Composer\Command;

use Nedbase\Composer\Report\JUnit;
use Nedbase\Composer\Report\ReportInterface;

class JUnitCommand extends ReportCommand
{
    protected function getReportName(): string
    {
        return 'junit';
    }

    protected function getReporter(): ReportInterface
    {
        return new JUnit();
    }

    public function getDescription(): string
    {
        return parent::getDescription().' and outputs the report in JUnit format';
    }
}
