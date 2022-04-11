<?php

namespace Lune\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigration extends Command {
    protected static $defaultName = "make:migration";

    protected static $defaultDescription = "Create a new migration";

    protected function configure() {
        $this->addArgument("name", InputArgument::REQUIRED, "Migration name");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument("name");
        app(Migrator::class)->make($name);

        return Command::SUCCESS;
    }
}
