<?php

namespace Tiagofv\ZApi\Commands;

use Illuminate\Console\Command;

class ZApiCommand extends Command
{
    public $signature = 'zapi';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
