<?php

namespace Nedbase\Composer\Report;

use Composer\Composer;
use Symfony\Component\Console\Output\OutputInterface;

class Trivy implements ReportInterface
{
    /**
     * @var Composer
     */
    private $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    public function generate(array $source, OutputInterface $output): void
    {
        $packages = $this->composer->getRepositoryManager()->getLocalRepository()->getPackages();
        $installedVersions = [];
        foreach ($packages as $package) {
            $installedVersions[$package->getName()] = $package->getPrettyVersion();
        }

        $result = [];

        if (isset($source['advisories'])) {
            foreach ($source['advisories'] as $packageName => $advisories) {
                foreach ($advisories as $advisory) {
                    $result[] = [
                        'VulnerabilityID' => $advisory['cve'],
                        'PkgName' => $packageName,
                        'InstalledVersion' => $installedVersions[$packageName] ?? 'Unknown',
                        'Title' => $advisory['title'],
                        'Severity' => strtoupper($advisory['severity'] ?? 'unknown'),
                        'References' => [$advisory['link']],
                    ];
                }
            }
        }

        if (isset($source['abandoned'])) {
            foreach ($source['abandoned'] as $abandoned => $replacement) {
                $result[] = [
                    'VulnerabilityID' => sprintf('abandoned-package-%s', preg_replace('/[^a-z0-9\-]+/', '-', $abandoned)),
                    'PkgName' => $abandoned,
                    'InstalledVersion' => $installedVersions[$abandoned] ?? 'Unknown',
                    'Title' => sprintf('Package "%s" is abandoned', $abandoned),
                    'Description' => null === $replacement
                        ? 'No replacement package was suggested'
                        : sprintf('Package "%s" was suggested as a replacement.', $replacement),
                    'Severity' => 'MEDIUM',
                ];
            }
        }

        $output->writeln(json_encode(
            [
                'Target' => 'composer.lock',
                'Vulnerabilities' => count($result) > 0 ? $result : null,
            ]
        ));
    }
}
