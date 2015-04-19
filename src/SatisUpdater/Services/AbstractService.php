<?php

namespace SatisUpdater\Services;

use Nette\Object;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractService extends Object
{

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * @param OutputInterface $output
	 */
	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}

	/**
	 * @param string $message
	 */
	public function writeln($message)
	{
		if ($this->output) {
			$this->output->writeln($message);
		}
	}

}