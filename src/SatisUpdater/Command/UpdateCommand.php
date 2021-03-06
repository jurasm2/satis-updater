<?php
namespace SatisUpdater\Command;

use SatisUpdater\Services\SatisUpdater;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * UpdateCommand
 * @package SatisUpdater\Commands
 */
class UpdateCommand extends ConsoleCommand
{
	/**
	 * @var SatisUpdater
	 */
	protected $satisUpdater;

	/**
	 * Constructor
	 * @param SatisUpdater $satisUpdater
	 */
	public function __construct(SatisUpdater $satisUpdater)
	{
		parent::__construct();
		$this->satisUpdater = $satisUpdater;
	}

	protected function configure()
	{
		$this
			->setName('update')
			->setDefinition(array(
				new InputArgument('composerJsonFile', InputArgument::REQUIRED, 'Satis repository name'),
				new InputArgument('apiEndpoint', InputArgument::REQUIRED, 'Satis maintenance REST API endpoint'),
				new InputOption('rewrite', 'r', InputOption::VALUE_NONE, 'This option causes rewriting of application\'s composer.json file. Use it wisely.')
			))
			->setDescription('Creates or updates composer repository via REST api');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->satisUpdater->setOutput($output);

		// get sync manager
		$composerJsonFile = $input->getArgument('composerJsonFile');
		$apiEndpoint = $input->getArgument('apiEndpoint');
		$rewrite = $input->getOption('rewrite');

		$this->satisUpdater->setComposerJsonFile($composerJsonFile);
		$this->satisUpdater->setApiEndpoint($apiEndpoint);
		$this->satisUpdater->update($rewrite);
	}
}