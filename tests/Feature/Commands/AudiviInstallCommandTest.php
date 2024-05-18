<?php

use App\Install\Requirement;
use App\Install\RequirementsChecker;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Mockery\MockInterface;
use Symfony\Component\Console\Helper\TableStyle;

use function Pest\Laravel\artisan;
use function Pest\Laravel\instance;

function mockDependencies(bool $failing = false): void
{
    if ($failing) {
        instance(RequirementsChecker::class, mock(RequirementsChecker::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkNpm')->andReturn([
                new Requirement('npm', '8', '0', false),
                new Requirement('node', '10', '10', true),
            ]);
        }));

        return;
    }

    instance(RequirementsChecker::class, mock(RequirementsChecker::class, function (MockInterface $mock) {
        $mock->shouldReceive('checkNpm')->andReturn([
            new Requirement('npm', '8', '8', true),
            new Requirement('node', '18', '18', true),
        ]);
    }));
}

it('fails when node / npm requirements are not met', function () {
    mockDependencies(true);

    artisan('audivi:install', ['domain' => 'https://audivi.test'])
        ->expectsOutputToContain('Some requirements are not met')
        ->assertFailed();

})->group('command', 'install');

it('shows a table of failed requirements', function () {
    mockDependencies(true);

    artisan('audivi:install', ['domain' => 'https://audivi.test'])
        ->expectsTable(
            ['Name', 'Required', 'Current', 'Status'],
            [['npm', '8', '0', '❌']],
            columnStyles: [
                2 => (new TableStyle())->setPadType(STR_PAD_BOTH),
                3 => (new TableStyle())->setPadType(STR_PAD_BOTH),
            ]
        )
        ->expectsOutputToContain('Some requirements are not met')
        ->assertFailed();
})->group('command', 'install');

it('checks the database credentials', function () {
    mockDependencies();

    File::shouldReceive('exists')->once()->with('.env')->andReturn(false);
    File::shouldReceive('copy')->once()->with('.env.example', '.env');
    File::shouldReceive('get')->once()->with('.env')->andReturn(file_get_contents(__DIR__.'/.env.stub'));
    // Artisan::shouldReceive('call')->with('key:generate');

    artisan('audivi:install', ['domain' => 'https://audivi.test'])
        ->expectsOutputToContain('Creating .env...')
        ->expectsOutputToContain('Configure Database ...')
        ->expectsQuestion('What Database Engine do you use?', 'mysql')
        ->expectsQuestion('Database Host', 'localhost')
        ->expectsQuestion('Database Port', '3306')
        ->expectsQuestion('Database Name', 'audivi')
        ->expectsQuestion('Database User', 'root')
        ->expectsQuestion('Database Password', 'root')
        ->expectsConfirmation('Overwrite existing database?', 'yes')
        ->expectsOutputToContain('Checking Database connection ...')
        ->expectsOutputToContain('Could not connect to database, please check your credentials')
        ->assertFailed();

})->group('command', 'install');

it('does not overwrite an existing sqlite database', function () {
    mockDependencies();

    File::shouldReceive('exists')->once()->with('.env')->andReturn(false);
    File::shouldReceive('copy')->once()->with('.env.example', '.env');
    File::shouldReceive('get')->once()->with('.env')->andReturn(file_get_contents(__DIR__.'/.env.stub'));
    // Artisan::shouldReceive('call')->with('key:generate');

    artisan('audivi:install', ['domain' => 'https://audivi.test'])
        ->expectsOutputToContain('Creating .env...')
        ->expectsOutputToContain('Configure Database ...')
        ->expectsQuestion('What Database Engine do you use?', 'sqlite')
        ->expectsConfirmation('Overwrite existing database?', 'no')
        ->expectsOutputToContain('Aborted')
        ->assertFailed();

})->group('command', 'install');

it('installs the application', function () {
    mockDependencies();

    // Make sure the .env is created
    File::shouldReceive('exists')->once()->with('.env')->andReturn(false);
    File::shouldReceive('copy')->once()->with('.env.example', '.env');
    File::shouldReceive('get')->once()->with('.env')->andReturn(file_get_contents(__DIR__.'/.env.stub'));

    // Make sure the needed commands are executed
    Process::shouldReceive('run')->once()->with('php artisan key:generate --ansi', null);
    Process::shouldReceive('run')->once()->with('php artisan migrate:fresh --ansi --force', null);
    Process::shouldReceive('run')->once()->with('npm ci', null);
    Process::shouldReceive('run')->once()->with('npm run build', null);

    // Make sure the database is created
    File::shouldReceive('exists')->once()->with('database/database.sqlite')->andReturn(false);
    File::shouldReceive('put')->once()->with('database/database.sqlite', '');

    // Make sure the .env is updated
    File::shouldReceive('put')->once()->with('.env', file_get_contents(__DIR__.'/.env.sqlite.stub'));
    File::shouldReceive('put')->once()->with('.env', preg_replace('/APP_ENV=local/', 'APP_ENV=production', file_get_contents(__DIR__.'/.env.sqlite.stub')));

    artisan('audivi:install', ['domain' => 'https://audivi.test'])
        ->expectsOutputToContain('Creating .env...')
        ->expectsOutputToContain('Configure Database ...')
        ->expectsQuestion('What Database Engine do you use?', 'sqlite')
        ->expectsConfirmation('Overwrite existing database?', 'yes')
        ->expectsOutputToContain('Create Database ...')
        ->expectsOutputToContain('Create Admin...')
        ->expectsQuestion('Username', 'admin')
        ->expectsQuestion('E-Mail', 'admin@local.test')
        ->expectsQuestion('Password', 'test1234')
        ->expectsOutputToContain('Build Assets ...')
        ->expectsOutputToContain('Cleanup')
        ->expectsOutputToContain('Audivi was installed successfully')
        ->assertSuccessful();

    $users = User::all();
    expect($users)->toHaveCount(1)
        ->and($users->first()->name)->toBe('admin')
        ->and($users->first()->email)->toBe('admin@local.test')
        ->and($users->first()->password)->not->toBe('test1234');
})->group('command', 'install');
