<?php

declare(strict_types=1);

namespace App\Install;

/**
 * Install composer dependencies or composer itself.
 *
 * !Note: Could be used by a web installer, ie when composer or dependencies are not installed yet
 */
final class Composer
{
    public function __construct(private readonly string $appRoot, private readonly bool $passThrough = false)
    {
    }

    public static function hasComposer(): bool
    {
        return ! empty(shell_exec('composer --version'));
    }

    /**
     * @throws InstallException if we could not install composer correctly
     */
    public function selfInstall(): string
    {
        copy('https://getcomposer.org/installer', 'composer-setup.php');
        $hash = 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6';
        if (hash_file('sha384', 'composer-setup.php') !== $hash) {
            unlink('composer-setup.php');
            throw new InstallException('composer installer checksum failed', step: InstallationStep::COMPOSER_INSTALL);
        }

        $output = shell_exec('php composer-setup.php --install-dir='.$this->appRoot);
        unlink('composer-setup.php');

        if (empty($output)) {
            throw new InstallException('composer installer failed', step: InstallationStep::COMPOSER_INSTALL);
        }

        return $output;
    }

    /**
     * @param  string|null  $output
     *
     * @throws InstallException if the dry-run command could not be executed successfully.
     */
    public function dryRun(?string $composer = null, &$output = null): void
    {
        $composer = $composer ? $this->appRoot.'/composer.phar' : 'composer';
        $output = $this->exec(
            "cd {$this->appRoot} && {$composer} install --no-interaction --prefer-dist --dry-run 2>&1"
        );
    }

    /**
     * @param  string|null  $output
     *
     * @throws InstallException if the install command could not be executed successfully.
     */
    public function install(?string $composer = null, &$output = null): void
    {
        $composer = $composer ? $this->appRoot.'/composer.phar' : 'composer';
        $output = $this->exec(
            "cd {$this->appRoot} && {$composer} install --no-interaction --prefer-dist --optimize-autoloader 2>&1"
        );
    }

    /**
     * @throws InstallException if the command could not be executed successfully.
     */
    private function exec(string $command): ?string
    {
        if ($this->passThrough) {
            $result = passthru($command);

            if ($result !== null) {
                throw new InstallException(
                    "Could not execute command: {$command}",
                    step: InstallationStep::COMPOSER_INSTALL
                );
            }

            return null;
        }

        $result = exec($command, $output, $exitCode);

        if (empty($result) || $exitCode !== 0) {
            throw new InstallException(
                "Could not execute command: {$command}",
                step: InstallationStep::COMPOSER_INSTALL
            );
        }

        return $result;
    }
}
