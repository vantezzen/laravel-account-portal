<?php

namespace Vantezzen\LaravelAccountPortal\Commands;

use Illuminate\Console\Command;

class LaravelAccountPortalCommand extends Command
{
    public $signature = 'laravel-account-portal';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
