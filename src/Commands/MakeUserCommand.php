<?php

namespace Cachet\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:make:user {email?} {--password=}';

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
     * Execute the console command.
     */
    public function handle()
    {
        if ($password = $this->option('password')) {
            $this->password = $password;
        }

        if ($this->email = $this->argument('email')) {
            $this->createUser();

            return;
        }

        $this
            ->promptEmail()
            ->promptName()
            ->promptPassword()
            ->createUser();
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
     * Create the user.
     */
    protected function createUser(): void
    {
        $userModel = config('cachet.user_model');

        $userModel::create([
            'name' => $this->data['name'],
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        $this->components->info('User created successfully.');
    }
}
