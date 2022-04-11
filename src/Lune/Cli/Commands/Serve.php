<?php

namespace Lune\Cli\Commands;

use Lune\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Serve extends Command {
    protected static $defaultName = "serve";

    protected static $defaultDescription = "Run Lune development application";

    protected function configure() {
        $this
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "Host address to run on", "127.0.0.1")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "Port to run on", "8080");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $host = $input->getOption("host");
        $port = $input->getOption("port");
        $dir = App::$ROOT . "/public";

        $output->writeln("<info>Starting development server on $host:$port</info>");
        shell_exec("php -S $host:$port $dir/index.php");

        return Command::SUCCESS;
    }
}
