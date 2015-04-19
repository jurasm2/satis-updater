<?php

$autoloader = require __DIR__ . '/../vendor/autoload.php';
// let composer autoload project's files
$autoloader->add('', __DIR__);

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
$console = new \Symfony\Component\Console\Application();

$console->setName(APP_NAME);
$console->setVersion('1.0');


$configurationReader = new \Eloquent\Composer\Configuration\ConfigurationReader();
$satisUpdater = new \SatisUpdater\Services\SatisUpdater($configurationReader);

$console
	->register('update')
	->setDefinition(array(
		new InputArgument('composerJsonFile', InputArgument::REQUIRED, 'Satis repository name'),
		new InputArgument('apiEndpoint', InputArgument::REQUIRED, 'Satis maintenance REST API endpoint'),
		new InputOption('rewrite', 'r', InputOption::VALUE_NONE, 'This option causes rewriting of application\'s composer.json file. Use it wisely.')
	))
	->setDescription('Creates or updates composer repository via REST api')
	->setCode(function (InputInterface $input, OutputInterface $output) use ($satisUpdater) {
		$satisUpdater->setOutput($output);

		// get sync manager
		$composerJsonFile = $input->getArgument('composerJsonFile');
		$apiEndpoint = $input->getArgument('apiEndpoint');
		$rewrite = $input->getOption('rewrite');

		$satisUpdater->setComposerJsonFile($composerJsonFile);
		$satisUpdater->setApiEndpoint($apiEndpoint);
		$satisUpdater->update($rewrite);
	});


return $console;