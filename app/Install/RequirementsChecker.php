<?php

declare(strict_types=1);

namespace App\Install;

use JsonException;

/**
 * Check requirements like composer and node/npm.
 *
 * !Note: Could be used by a web installer, ie when composer or dependencies are not installed yet
 */
class RequirementsChecker
{
    /**
     * Check composer dependencies and platform requirements
     *
     * @return Requirement[]
     *
     * @throws InstallException if composer check-platform-reqs fails
     */
    public function checkComposer(string $appRoot, ?string $composer = null): array
    {
        $composer = $composer ? $appRoot.'/composer.phar' : 'composer';
        $dryRun = shell_exec("cd {$appRoot} && {$composer} check-platform-reqs --format=json");

        if (empty($dryRun)) {
            throw new InstallException(
                'composer check-platform-reqs failed',
                step: InstallationStep::CHECK_REQUIREMENTS
            );
        }

        try {
            $reqs = json_decode($dryRun, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InstallException(
                'composer check-platform-reqs failed',
                step: InstallationStep::CHECK_REQUIREMENTS
            );
        }

        return array_map(
            static fn (array $req) => new Requirement(
                $req['name'],
                $req['failed_requirement']['constraint'] ?? $req['version'],
                $req['version'],
                $req['status'] === 'success'
            ),
            $reqs
        );
    }

    /**
     * Check npm and node versions
     *
     * @return Requirement[]
     *
     * @throws InstallException if npm or node check fails
     */
    public function checkNpm(string $appRoot): array
    {
        try {
            $nodeReqs = json_decode(
                file_get_contents($appRoot.'/package.json'),
                true,
                flags: JSON_THROW_ON_ERROR
            )['engines'] ?? [];

            $node = shell_exec('node -v');
            $npm = shell_exec('npm -v');
        } catch (JsonException|\Throwable $err) {
            throw new InstallException($err->getMessage(), step: InstallationStep::CHECK_REQUIREMENTS);
        }

        return [
            new Requirement(
                'node',
                $nodeReqs['node'],
                trim($node),
                $node && version_compare(trim($node, "v\n\r\t<>=~^"), trim($nodeReqs['node'], "v\n\r\t<>=~^"), '>=')
            ),
            new Requirement(
                'npm',
                $nodeReqs['npm'],
                trim($npm),
                $npm && version_compare(trim($npm, "v\n\r\t<>=~^"), trim($nodeReqs['npm'], "v\n\r\t<>=~^"), '>=')
            ),
        ];
    }
}
