<?php

namespace Nedbase\Composer\Command;

use Nedbase\Composer\Report\ReportInterface;
use Nedbase\Composer\Report\Trivy;

class TrivyCommand extends ReportCommand
{

    protected function getReportName(): string
    {
        return 'trivy';
    }

    protected function getReporter(): ReportInterface
    {
        return new Trivy($this->requireComposer());
    }

    public function getDescription(): string
    {
        return parent::getDescription().' and outputs the report in Trivy format';
    }
}
