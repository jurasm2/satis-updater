<?php

$autoloader = require __DIR__ . '/../vendor/autoload.php';
// let composer autoload project's files
$autoloader->add('', __DIR__);

use Symfony\Component\Console\Application;
use Eloquent\Composer\Configuration\ConfigurationReader;
use SatisUpdater\Services\SatisUpdater;
use SatisUpdater\Command;

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
$console = new Application(APP_NAME, '0.9.1');

// instantiate services
$configurationReader = new ConfigurationReader();
$satisUpdater = new SatisUpdater($configurationReader);

$console->add(new Command\CreateCommand($satisUpdater));
$console->add(new Command\UpdateCommand($satisUpdater));
$console->add(new Command\SelfUpdateCommand());

return $console;
