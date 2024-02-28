<?php

namespace Nedbase\Composer\Report;

use Nedbase\Composer\Exception\ParseException;
use Symfony\Component\Console\Output\OutputInterface;

abstract class JsonBasedReporter implements ReportInterface
{
    abstract public function generate(string $source, OutputInterface $output): void;

    /**
     * @return array{advisories?: array<string, array<array{cve: string, title: string, link: string, severity?: string, affectedVersions: string, reportedAt: string}>>, abandoned?: array<string, string|null>} $source
     *
     * @throws ParseException
     */
    protected function parseSource(string $source): array
    {
        $startPos = strpos($source, '{'.PHP_EOL);
        if ($startPos > 0) {
            $source = substr($source, $startPos);
        }

        /** @var array{advisories?: array<string, array<array{cve: string, title: string, link: string, severity?: string, affectedVersions: string, reportedAt: string}>>, abandoned?: array<string, string|null>} $result */
        $result = json_decode($source, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new ParseException('Failed to parse JSON data: '.json_last_error_msg(), json_last_error());
        }

        return $result;
    }
}
