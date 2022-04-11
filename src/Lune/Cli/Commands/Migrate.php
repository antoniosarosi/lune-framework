<?php

namespace Lune\Cli\Commands;

use Lune\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command {
    protected static $defaultName = "migrate";

    protected static $defaultDescription = "Run migrations";

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            app(Migrator::class)->migrate();
            return Command::SUCCESS;
        } catch (\PDOException $e) {
            $output->writeln("<error>Could not run migratins: {$e->getMessage()}</error>");
            $output->write($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
