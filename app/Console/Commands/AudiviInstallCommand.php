<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Install\InstallationStep;
use App\Install\InstallerService;
use App\Install\InstallException;
use App\Install\Requirement;
use App\Install\RequirementsChecker;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\form;
use function Laravel\Prompts\select;

class AudiviInstallCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'audivi:install
        {domain : The domain of the application}
        {locale=en : The locale of the application}
        {--db=sqlite : The database driver of the application}
        {--db-host=127.0.0.1 : The database host of the application}
        {--db-port=3306 : The database port of the application}
        {--db-name=audivi : The database name of the application}
        {--db-username=root : The database username of the application}
        {--admin=admin : The username of the admin user}
        {--admin-email= : The email of the admin user}
    ';

    protected $description = 'Command description';

    public function __construct(private readonly InstallerService $installer)
    {
        parent::__construct();
    }

    public function handle(RequirementsChecker $requirements): int
    {
        $this->installer->setOutputBuffer(
            $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE
                ? fn (string $type, string $output) => $this->getOutput()->write($output)
                : null
        );

        try {
            $this->checkRequirements($requirements);
            $this->setupEnvironment();
            $this->configureDatabase();
        } catch (InstallException $exc) {
            $this->print($exc->getMessage(), 'error');

            return self::FAILURE;
        }

        $this->installer->saveEnvironment();

        $this->print('Create Database ...');
        $this->installer->createDatabase();

        $this->createAdmin();
        $this->buildAssets();
        $this->cleanup();

        // Success
        $this->newLine();
        $this->print('Audivi was installed successfully', 'success');

        return self::SUCCESS;
    }

    private function setupEnvironment(): void
    {
        $this->print('Creating .env...');
        $this->installer->setupEnvironment($this->argument('locale'), $this->argument('domain'));
    }

    /**
     * @throws InstallException if requirements could not be checked or some are not met
     */
    private function checkRequirements(RequirementsChecker $requirements): void
    {
        $this->print('Checking Requirements...');
        $reqs = $this->installer->checkRequirements();
        $invalidReqs = collect($reqs)->filter(fn (Requirement $req) => ! $req->valid);

        if ($invalidReqs->isNotEmpty() || $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->table(
                ['Name', 'Required', 'Current', 'Status'],
                \Arr::map(
                    $reqs,
                    static fn (Requirement $req) => [$req->name, $req->version, $req->current, $req->valid ? '✅' : '❌']
                ),
                columnStyles: [
                    2 => (new TableStyle())->setPadType(\STR_PAD_BOTH),
                    3 => (new TableStyle())->setPadType(\STR_PAD_BOTH),
                ]
            );
        }

        if ($invalidReqs->isNotEmpty()) {
            throw new InstallException('Some requirements are not met', step: InstallationStep::CHECK_REQUIREMENTS);
        }
    }

    /**
     * @throws InstallException if database could not be configured
     */
    private function configureDatabase(): void
    {
        $this->print('Configure Database ...');
        $engine = select(
            label: 'What Database Engine do you use?',
            options: [
                'sqlite' => 'SQLite',
                'mysql' => 'MySQL / MariaDB',
                'pqsql' => 'PostgreSQL',
            ],
            default: $this->option('db') ?? 'sqlite',
            required: true,
        );

        if ($engine === 'sqlite') {
            $confirmation = confirm('Overwrite existing database?', default: false);
            $this->installer->configureDatabase($confirmation, $engine);
        }

        if ($engine !== 'sqlite') {
            $database = form()
                ->text('Database Host', default: $this->option('db-host') ?? '', required: true, name: 'host')
                ->text('Database Port', default: $this->option('db-port') ?? '', required: true, name: 'port')
                ->text('Database Name', default: $this->option('db-name') ?? '', required: true, name: 'database')
                ->text('Database User', default: $this->option('db-username') ?? '', required: true, name: 'user')
                ->password('Database Password', required: true, name: 'password')
                ->confirm('Overwrite existing database?', name: 'confirmation')
                ->submit();

            $this->print('Checking Database connection ...');
            $this->installer->configureDatabase(
                $database['confirmation'],
                $engine,
                $database['host'],
                $database['port'],
                $database['database'],
                $database['user'],
                $database['password']
            );
        }
    }

    private function buildAssets(): void
    {
        $this->newLine();
        $this->print('Build Assets ...');

        $this->installer->buildAssets();
    }

    private function cleanup(): void
    {
        $this->newLine();
        $this->print('Cleanup');
        $this->installer->cleanup();
    }

    private function createAdmin(): void
    {
        $this->print('Create Admin...');
        $admin = form()
            ->text('Username', default: $this->option('admin') ?? '', required: true, name: 'user')
            ->text(
                'E-Mail',
                default: $this->option('admin-email') ?? '',
                required: true,
                validate: ['email'],
                name: 'email'
            )
            ->password('Password', required: true, validate: [Password::default()], name: 'password')
            ->submit();

        $this->installer->createAdmin($admin['user'], $admin['email'], $admin['password']);
    }

    private function print(string $message, ?string $style = null): void
    {
        $prefix = match ($style) {
            'success' => '  <fg=white;bg=green;options=bold> SUCCESS </>',
            'error' => '  <fg=white;bg=red;options=bold> ERROR </>',
            default => '  <fg=white;bg=blue;options=bold> INFO </>',
        };

        $this->getOutput()->writeln($prefix.' '.$message);
    }
}
