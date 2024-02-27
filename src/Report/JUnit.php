<?php

namespace Nedbase\Composer\Report;

use Symfony\Component\Console\Output\OutputInterface;

class JUnit implements ReportInterface
{
    /**
     * @var \DOMDocument
     */
    private $document;

    public function __construct()
    {
        $this->document = new \DOMDocument();
        $this->document->formatOutput = true;
    }

    public function generate(array $source, OutputInterface $output): void
    {
        $suites = $this->document->appendChild($this->document->createElement('testsuites'));

        $advisories = $this->advisories($source['advisories'] ?? []);
        if (null !== $advisories) {
            $suites->appendChild($advisories);
        }

        $abandonedPackages = $this->abandoned($source['abandoned'] ?? []);
        if (null !== $abandonedPackages) {
            $suites->appendChild($abandonedPackages);
        }

        $output->writeln($this->document->saveXML());
    }

    /**
     * @param array<string, array<array{cve: string, title: string, link: string, severity?: string, affectedVersions: string, reportedAt: string}>> $advisories
     * @return \DOMNode|null
     * @throws \DOMException
     */
    private function advisories(array $advisories): ?\DOMNode
    {
        if (0 === count($advisories)) {
            return null;
        }

        $report = $this->document->createElement('testsuite');
        $report->setAttribute('name', 'Security Vulnerability Advisories');

        foreach ($advisories as $package => $packageAdvisories) {
            $testcase = $this->document->createElement('testcase');
            $testcase->setAttribute('name', $package);
            foreach ($packageAdvisories as $advisory) {
                $failure = $this->document->createElement('failure');
                $failure->setAttribute('message', $advisory['title']);
                $failure->appendChild(new \DOMText($this->generateAdvisoryDescription($advisory)));
                $testcase->appendChild($failure);
            }
            $report->appendChild($testcase);
        }

        return $report;
    }

    /**
     * @param array<string, string|null> $packages
     * @return \DOMNode|null
     * @throws \DOMException
     */
    private function abandoned(array $packages): ?\DOMNode
    {
        if (0 === count($packages)) {
            return null;
        }

        $report = $this->document->createElement('testsuite');
        $report->setAttribute('name', 'Abandoned packages');

        foreach ($packages as $abandoned => $replacement) {
            $testcase = $this->document->createElement('testcase');
            $testcase->setAttribute('name', $abandoned);
            $failure = $this->document->createElement('failure');
            $failure->setAttribute('message', $abandoned);
            if (null === $replacement) {
                $failure->appendChild(new \DOMText('No replacement was suggested.'));
            } else {
                $failure->appendChild(new \DOMText(sprintf('Package is replaced by %s.', $replacement)));
            }
            $testcase->appendChild($failure);
            $report->appendChild($testcase);
        }

        return $report;
    }

    /**
     * @param array{cve: string, title: string, link: string, severity?: string, affectedVersions: string, reportedAt: string} $advisory
     * @return string
     */
    private function generateAdvisoryDescription(array $advisory): string
    {
        return join(PHP_EOL, [
            sprintf('CVE: %s', $advisory['cve']),
            sprintf('Affected version(s): %s', $advisory['affectedVersions']),
            sprintf('Link: %s', $advisory['link']),
            sprintf('Reported at: %s', $advisory['reportedAt']),
        ]);
    }
}
