<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$container = require __DIR__ . '/app/bootstrap.php';

const APP_NAME = "
  ██████  ▄▄▄      ▄▄▄█████▓ ██▓  ██████     █    ██  ██▓███  ▓█████▄  ▄▄▄      ▄▄▄█████▓▓█████  ██▀███
▒██    ▒ ▒████▄    ▓  ██▒ ▓▒▓██▒▒██    ▒     ██  ▓██▒▓██░  ██▒▒██▀ ██▌▒████▄    ▓  ██▒ ▓▒▓█   ▀ ▓██ ▒ ██▒
░ ▓██▄   ▒██  ▀█▄  ▒ ▓██░ ▒░▒██▒░ ▓██▄      ▓██  ▒██░▓██░ ██▓▒░██   █▌▒██  ▀█▄  ▒ ▓██░ ▒░▒███   ▓██ ░▄█ ▒
  ▒   ██▒░██▄▄▄▄██ ░ ▓██▓ ░ ░██░  ▒   ██▒   ▓▓█  ░██░▒██▄█▓▒ ▒░▓█▄   ▌░██▄▄▄▄██ ░ ▓██▓ ░ ▒▓█  ▄ ▒██▀▀█▄
▒██████▒▒ ▓█   ▓██▒  ▒██▒ ░ ░██░▒██████▒▒   ▒▒█████▓ ▒██▒ ░  ░░▒████▓  ▓█   ▓██▒  ▒██▒ ░ ░▒████▒░██▓ ▒██▒
▒ ▒▓▒ ▒ ░ ▒▒   ▓▒█░  ▒ ░░   ░▓  ▒ ▒▓▒ ▒ ░   ░▒▓▒ ▒ ▒ ▒▓▒░ ░  ░ ▒▒▓  ▒  ▒▒   ▓▒█░  ▒ ░░   ░░ ▒░ ░░ ▒▓ ░▒▓░
░ ░▒  ░ ░  ▒   ▒▒ ░    ░     ▒ ░░ ░▒  ░ ░   ░░▒░ ░ ░ ░▒ ░      ░ ▒  ▒   ▒   ▒▒ ░    ░     ░ ░  ░  ░▒ ░ ▒░
░  ░  ░    ░   ▒     ░       ▒ ░░  ░  ░      ░░░ ░ ░ ░░        ░ ░  ░   ░   ▒     ░         ░     ░░   ░
      ░        ░  ░          ░        ░        ░                 ░          ░  ░            ░  ░   ░
                                                               ░
Bloody handy satis updater
";

/** @var \Symfony\Component\Console\Application $console */
$console = $container->getService('consoleApplication');

$console->setName(APP_NAME);
$console->setVersion('1.0');

//\Tracy\Debugger::enable(FALSE);

$console
	->register('update')
	->setDefinition(array(
		new InputArgument('composerJsonFile', InputArgument::REQUIRED, 'Satis repository name'),
		new InputArgument('apiEndpoint', InputArgument::REQUIRED, 'Satis maintenance REST API endpoint'),
		new InputOption('rewrite', 'r', InputOption::VALUE_NONE, 'This option causes rewriting of application\'s composer.json file. Use it wisely.')
	))
	->setDescription('Creates or updates composer repository via REST api')
	->setCode(function (InputInterface $input, OutputInterface $output) use ($container) {

		$satisUpdater = $satisGenerator = $container->getService('satisUpdater');

		// get sync manager
		$composerJsonFile = $input->getArgument('composerJsonFile');
		$apiEndpoint = $input->getArgument('apiEndpoint');
		$rewrite = $input->getOption('rewrite');

		$satisUpdater->setComposerJsonFile($composerJsonFile);
		$satisUpdater->setApiEndpoint($apiEndpoint);
		$satisUpdater->update($rewrite);
	});

$console->run();