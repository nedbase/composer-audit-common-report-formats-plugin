<?php

namespace Nedbase\Test\Composer\Report;

use Nedbase\Composer\Report\JUnit;
use PHPUnit\Framework\DOMTestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class JUnitTest extends DOMTestCase
{
    public function testGenerate(): void
    {
        $reporter = new JUnit();
        $source = json_decode(file_get_contents(FIXTURES_PATH.'/advisories-and-abandoned-packages.json'), true);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);

        $reporter->generate($source, $output);
        $xml = $output->fetch();

        $this->assertSelectCount(
            'testsuite[name="Security Vulnerability Advisories"] testcase',
            1,
            $xml,
            '',
            false
        );

        $this->assertSelectCount(
            'testsuite[name="Security Vulnerability Advisories"] testcase[name="vendor1/package1"] failure',
            2,
            $xml,
            '',
            false
        );

        $this->assertSelectCount(
            'testsuite[name="Abandoned packages"] testcase',
            1,
            $xml,
            '',
            false
        );

        $this->assertSelectCount(
            'testsuite[name="Abandoned packages"] testcase[name="vendor2/package1"] failure',
            1,
            $xml,
            '',
            false
        );
    }
}
