# JUnit formatted output for Composer Audit

This package provides a JUnit formatted audit report for the `composer audit` command, that was introduced in
Composer 2.4

## Installation

You can either require the package globally or as a dev dependency for your project.

To make the plugin globally available run the following command:

```shell
composer global require nedbase/composer-audit-junit-plugin 
```

To add the plugin available for a specific project, you may add it as a dev dependency:

```shell
composer require --dev nedbase/composer-audit-junit-plugin
```

## Generating a JUnit formatted audit report

To generate a JUnit formatted audit report, run the following command:

```
composer audit:junit
```

The same options and arguments that exist on Composer's native `audit` command are available for the `audit:junit`
command, except for the `--format` option, as the report format is implied by the `audit:junit` command itself. So to
generate a JUnit formatted audit report of regular dependencies only (so no dev dependencies) you may run:

```shell
composer audit:junit --no-dev
```
