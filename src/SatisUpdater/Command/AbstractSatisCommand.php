<?php
namespace SatisUpdater\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use SatisUpdater\Services\SatisUpdater;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractSatisCommand extends ConsoleCommand
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

	/**
	 * @param array $arrayOfAdditionalDefinitions
	 * @return ConsoleCommand
	 */
	protected function setDefaultDefinition(array $arrayOfAdditionalDefinitions = [])
	{
		$definitionsToAdd = $this->getDefaultDefinition();
		if (!empty($arrayOfAdditionalDefinitions)) {
			$definitionsToAdd = array_merge($definitionsToAdd, $arrayOfAdditionalDefinitions);
		}

		return $this->setDefinition($definitionsToAdd);
	}

	/**
	 * @return array
	 */
	protected function getDefaultDefinition()
	{
		return [
			new InputOption('appDir', 'a', InputOption::VALUE_OPTIONAL, 'Path to application directory. The composer.json should reside in.', '.'),
			new InputOption('restApiUrl', 'r', InputOption::VALUE_OPTIONAL, 'REST API endpoint providing satis repository maintenance', 'https://lnd.bz/git-releases/rest')
		];
	}

	protected function getDefaultOptions(InputInterface $input)
	{
		$appDir = $input->getOption('appDir');
		$restApiUrl = $input->getOption('restApiUrl');

		return [$appDir, $restApiUrl];
	}

}