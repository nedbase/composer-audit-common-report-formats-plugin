<?php

namespace Nedbase\Composer\Command;

use Composer\Command\AuditCommand;
use Composer\IO\BufferIO;
use Composer\IO\IOInterface;
use Nedbase\Composer\Report\ReportInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ReportCommand extends AuditCommand
{

    private InputOption $formatOption;

    private ?IOInterface $bufferedIO = null;

    abstract protected function getReportName(): string;

    abstract protected function getReporter(): ReportInterface;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->bufferedIO = new BufferIO();
    }

    protected function getBaseReportFormat(): string
    {
        return 'json';
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('audit:'.$this->getReportName());

        $options = $this->getDefinition()->getOptions();
        $this->formatOption = $options['format'];
        unset($options['format']);
        $this->getDefinition()->setOptions($options);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $def = $this->getDefinition();
        $def->addOption($this->formatOption);
        $this->setDefinition($def);
        $input->setOption('format', $this->getBaseReportFormat());

        $this->bufferedIO = new BufferIO();
        parent::execute($input, $output);
        $parentOutput = $this->bufferedIO->getOutput();
        $startPos = strpos($parentOutput, '{'.PHP_EOL);
        if ($startPos > 0) {
            $parentOutput = substr($parentOutput, $startPos);
        }

        $auditData = json_decode($parentOutput, true, 512, JSON_THROW_ON_ERROR);
        $this->bufferedIO = null;

        $this->getReporter()->generate($auditData, $output);

        return Command::SUCCESS;
    }

    public function getIO()
    {
        return $this->bufferedIO ?? parent::getIO();
    }
}
