<?php

namespace Lune\Cli\Commands;

use Lune\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollback extends Command {
    protected static $defaultName = "migrate:rollback";

    protected static $defaultDescription = "Rollback migrations";

    protected function configure() {
        $this->addArgument("steps", InputArgument::OPTIONAL, "Amount of migrations to reverse, all by default");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            app(Migrator::class)->rollback($input->getArgument("steps") ?? null);
            return Command::SUCCESS;
        } catch (\PDOException $e) {
            $output->writeln("<error>Could not reverse migrations</error>: {$e->getMessage()}");
            $output->write($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
