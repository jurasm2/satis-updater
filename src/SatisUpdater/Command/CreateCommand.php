<?php

namespace SatisUpdater\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateCommand extends AbstractSatisCommand
{

	public function configure()
	{
		$this
			->setName('create')
			->setDefaultDefinition([
				new InputOption('rewrite', 'w', InputOption::VALUE_NONE, 'This option causes rewriting of application\'s composer.json file. Use it wisely.')
			])
			->setDescription('Creates (initializes) composer repository via REST api');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->satisUpdater->setOutput($output);

		list($appDir, $restApiUrl) = $this->getDefaultOptions($input);

		$this->satisUpdater->setAppDir($appDir);
		$this->satisUpdater->setApiEndpoint($restApiUrl);
		$rewrite = $input->getOption('rewrite');

		$returnCode = 0;
		try {
			$this->satisUpdater->create($rewrite);
		} catch(\Exception $ex) {
			$output->writeln('<error>' . $ex->getMessage() . '</error>');
			$returnCode = $ex->getCode();
		}

		return $returnCode;
	}

}