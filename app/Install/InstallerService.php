<?php

declare(strict_types=1);

namespace App\Install;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PDO;
use PDOException;
use SensitiveParameter;

final class InstallerService
{
    private string $environment = '';

    /** @var callable|null */
    private $output;

    public function __construct(private readonly RequirementsChecker $requirements)
    {
    }

    public function setOutputBuffer(?callable $output): void
    {
        $this->output = $output;
    }

    /**
     * @return Requirement[]
     * @throws InstallException if npm or node check fails
     */
    public function checkRequirements(): array
    {
        return $this->requirements->checkNpm(base_path());
    }

    public function process(string $command): void
    {
        Process::run($command, $this->output);
    }

    public function setupEnvironment(string $locale, string $domain): void
    {
        if (! File::exists('.env')) {
            File::copy('.env.example', '.env');
        }
        $this->process('php artisan key:generate --ansi');

        $env = File::get('.env');
        $env = preg_replace('/APP_LOCALE=en/', 'APP_LOCALE='.$locale, $env);
        $env = preg_replace('/APP_URL=http:\/\/localhost/', 'APP_URL='.$domain, $env);

        $this->environment = $env;
    }

    /**
     * @throws InstallException if the database could not be configured
     */
    public function configureDatabase(
        bool $confirmation,
        string $engine,
        ?string $host = null,
        ?string $port = null,
        ?string $database = null,
        ?string $username = null,
        #[SensitiveParameter]
        ?string $password = null
    ): void {
        $this->updateEnvironmentValue('DB_CONNECTION', $engine);

        if (! $confirmation) {
            throw new InstallException('Aborted database configuration', step: InstallationStep::CONFIGURE_DATABASE);
        }

        if ($engine === 'sqlite') {
            if (! File::exists('database/database.sqlite')) {
                File::put('database/database.sqlite', '');
            }

            return;
        }

        try {
            new PDO("{$engine}:host={$host};port={$port};dbname={$database}", $username, $password);
        } catch (PDOException) {
            throw new InstallException(
                'Could not connect to database, please check your credentials',
                step: InstallationStep::CONFIGURE_DATABASE
            );
        }

        $this->updateEnvironmentValue('DB_HOST', $host);
        $this->updateEnvironmentValue('DB_PORT', $port);
        $this->updateEnvironmentValue('DB_DATABASE', $database);
        $this->updateEnvironmentValue('DB_USERNAME', $username);
        $this->updateEnvironmentValue('DB_PASSWORD', $password);
    }

    public function saveEnvironment(): void
    {
        File::put('.env', $this->environment);

        config([
            'database.default' => $_ENV['DB_CONNECTION']
        ]);

        if ($_ENV['DB_CONNECTION'] !== 'sqlite') {
            config([
                    'database.connections.' .  $_ENV['DB_CONNECTION'] . '.host' => $_ENV['DB_HOST'],
                    'database.connections.' .  $_ENV['DB_CONNECTION'] . '.port' => $_ENV['DB_PORT'],
                    'database.connections.' .  $_ENV['DB_CONNECTION'] . '.database' => $_ENV['DB_DATABASE'],
                    'database.connections.' .  $_ENV['DB_CONNECTION'] . '.username' => $_ENV['DB_USERNAME'],
                    'database.connections.' .  $_ENV['DB_CONNECTION'] . '.password' => $_ENV['DB_PASSWORD'],
            ]);
        }
        config(['telescope.storage.database.connection' => $_ENV['DB_CONNECTION']]); // fix telescope issue
    }

    public function createDatabase(): void
    {
        $this->process('php artisan migrate:fresh --ansi --force');
    }

    public function createAdmin(string $name, string $email, #[SensitiveParameter] string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function buildAssets(): void
    {
        $this->process('npm ci');
        $this->process('npm run build');
    }

    public function cleanup(): void
    {
        $this->environment = preg_replace('/APP_ENV=local/', 'APP_ENV=production', $this->environment);
        File::put('.env', $this->environment);
    }

    private function updateEnvironmentValue(string $key, string $value): void
    {
        $_ENV[$key] = $value;
        $this->environment = preg_replace("/# {$key}=/", "{$key}={$value}", $this->environment);
    }
}
