<?php

namespace Lune\Cli\Commands;

use Lune\App;
use Lune\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModel extends Command {
    protected static $defaultName = "make:model";

    protected static $defaultDescription = "Create a new model";

    protected function configure() {
        $this
            ->addArgument("name", InputArgument::REQUIRED, "Migration name")
            ->addOption("migration", "m", InputOption::VALUE_OPTIONAL, "Also create migration file", false);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument("name");
        $migration = $input->getOption("migration");

        $template = str_replace("ModelName", $name, template("model"));
        file_put_contents(App::$ROOT . "/app/Models/$name.php", $template);
        $output->writeln("<info>Model created => $name.php</info>");

        if ($migration !== false) {
            app(Migrator::class)->make("create_{$name}s_table");
        }

        return Command::SUCCESS;
    }
}
