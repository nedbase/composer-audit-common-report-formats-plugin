<?php

namespace Nedbase\Composer\Command;

use Composer\Command\AuditCommand;
use Composer\IO\BufferIO;
use Composer\IO\IOInterface;
use Nedbase\Composer\Exception\ParseException;
use Nedbase\Composer\Report\ReportInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ReportCommand extends AuditCommand
{
    /**
     * @var ?InputOption
     */
    private $formatOption;

    /**
     * @var IOInterface|null
     */
    private $bufferedIO;

    abstract protected function getReportName(): string;

    abstract protected function getReporter(): ReportInterface;

    public function __construct(?string $name = null)
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
        $def->addOption($this->formatOption ?? new InputOption('format', 'f', InputOption::VALUE_REQUIRED, '', 'table'));
        $this->setDefinition($def);
        $input->setOption('format', $this->getBaseReportFormat());

        $this->bufferedIO = new BufferIO();
        $exitCode = parent::execute($input, $output);
        $parentOutput = $this->bufferedIO->getOutput();
        $this->bufferedIO = null;

        try {
            $this->getReporter()->generate($parentOutput, $output);
        } catch (ParseException $exception) {
            if ($output instanceof ConsoleOutputInterface) {
                $output->getErrorOutput()->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            } else {
                $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            }

            return 255;
        }

        return $exitCode;
    }

    public function getIO()
    {
        return $this->bufferedIO ?? parent::getIO();
    }
}
