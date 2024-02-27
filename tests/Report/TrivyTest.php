<?php

namespace Nedbase\Test\Composer\Report;

use Composer\Composer;
use Composer\Package\CompletePackage;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Repository\RepositoryManager;
use Nedbase\Composer\Report\Trivy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class TrivyTest extends TestCase
{
    public function testGenerate(): void
    {
        $reporter = new Trivy($this->buildMockComposer());
        $source = json_decode(file_get_contents(FIXTURES_PATH.'/advisories-and-abandoned-packages.json'), true);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);

        $reporter->generate($source, $output);
        $json = json_decode($output->fetch(), true);

        $this->assertArrayHasKey('Vulnerabilities', $json);

        $this->assertSame(
            array_column($json['Vulnerabilities'], 'VulnerabilityID'),
            ['CVE-2021-99999', 'CVE-2022-99999', 'abandoned-package-vendor2-package1']
        );

        $this->assertSame(
            array_column($json['Vulnerabilities'], 'PkgName'),
            ['vendor1/package1', 'vendor1/package1', 'vendor2/package1']
        );
    }

    /**
     * @return Composer
     */
    private function buildMockComposer()
    {
        $repository = $this->createMock(InstalledRepositoryInterface::class);
        $repository
            ->method('getPackages')
            ->willReturn([
                new CompletePackage('vendor1/package1', '5.4.0.0', 'v5.4.0'),
            ]);

        $repositoryManager = $this->createMock(RepositoryManager::class);
        $repositoryManager
            ->method('getLocalRepository')
            ->willReturn($repository);

        $composer = $this->createMock(Composer::class);
        $composer
            ->method('getRepositoryManager')
            ->willReturn($repositoryManager);

        return $composer;
    }
}
