<?php

namespace Cachet\Commands;

use Cachet\Cachet;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:make:user {email?} {--password= : The user\'s password} {--admin= : Whether the user is an admin} {--name= : The name of the user }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * The user's data.
     */
    protected array $data = [];

    /**
     * The user's email.
     */
    protected ?string $email = null;

    /**
     * The user's password.
     */
    protected ?string $password = null;

    /**
     * Whether the user is an admin.
     */
    protected ?bool $isAdmin = null;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->email = $this->argument('email');
        $this->isAdmin = $this->option('admin') !== null ? (bool) $this->option('admin') : null;
        $this->password = $this->option('password');
        $this->data['name'] = $this->option('name');

        if (! $this->data['name']) {
            $this->promptName();
        }

        if (! $this->email) {
            $this->promptEmail();
        }

        if ($this->isAdmin === null) {
            $this->promptIsAdmin();
        }

        if (! $this->password) {
            $this->promptPassword();
        }

        $this->createUser();

        return self::SUCCESS;
    }

    /**
     * Prompt the user for their email.
     */
    protected function promptEmail(): self
    {
        $this->email = text('What is the user\'s email?', required: true);

        return $this;
    }

    /**
     * Prompt the user for their name.
     */
    protected function promptName(): self
    {
        $this->data['name'] = text('What is the user\'s name?', required: true);

        return $this;
    }

    /**
     * Prompt the user for their password.
     */
    protected function promptPassword(): self
    {
        if ($this->password) {
            return $this;
        }

        $this->password = password('What is the user\'s password?', required: true);

        return $this;
    }

    /**
     * Prompt the user for whether they are an admin.
     */
    protected function promptIsAdmin(): self
    {
        $this->isAdmin = confirm('Is the user an admin?', default: false);

        return $this;
    }

    /**
     * Create the user.
     */
    protected function createUser(): void
    {
        $userModel = Cachet::userModel();

        $userModel::create([
            'name' => $this->data['name'],
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'is_admin' => $this->isAdmin,
        ]);

        $this->components->info('User created successfully.');
    }
}
