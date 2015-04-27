<?php
namespace SatisUpdater\Services;

use Eloquent\Composer\Configuration\ConfigurationReader;
use Httpful\Request;
use Httpful\Response;
use Nette\FileNotFoundException;
use Nette\InvalidStateException;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;


/**
 * Satis update script
 * this script posts a content of composer.json file to specified app maintaining composer repositories
  */
class SatisUpdater extends AbstractService
{
	/**
	 * Path to application directory
	 * @var string
	 */
	protected $appDir;

	/**
	 * Path to app's composer.json file
	 * @var string
	 */
	protected $composerJsonFile;

	/**
	 * Path to app's satis.json file
	 * @var string
	 */
	protected $satisJsonFile;

	/**
	 * Url of REST API endpoint
	 * @var string
	 */
	protected $apiEndpoint;

	/**
	 * @var ConfigurationReader
	 */
	protected $configurationReader;

	/**
	 * Constructor
	 * @param ConfigurationReader $configurationReader
	 */
	public function __construct(ConfigurationReader $configurationReader)
	{
		$this->configurationReader = $configurationReader;
	}

	/**
	 * @param string $appDir
	 */
	public function setAppDir($appDir)
	{
		$this->appDir = $appDir;
		$this->composerJsonFile = $appDir . '/composer.json';
		$this->satisJsonFile = $appDir . '/satis.json';
	}

	/**
	 * @param string $apiEndpoint
	 */
	public function setApiEndpoint($apiEndpoint)
	{
		$this->apiEndpoint = $apiEndpoint;
	}

	/**
	 * Sends a POST request for satis build
	 * @param bool $rewrite Rewrite original composer.json?
	 */
	public function create($rewrite = false)
	{
		if (!is_file($this->composerJsonFile)) {
			throw new FileNotFoundException("File composer.json not found. ('{$this->composerJsonFile}')", 255);
		}

		$this->output->writeln("<info>File '{$this->composerJsonFile}' found.</info>");

		// read the composer.json file and post it's content via REST API
		$config = $this->configurationReader->read($this->composerJsonFile);

		// create payload to send
		$payload = new \stdClass();
		$payload->{'name'} = Strings::webalize($config->name());
		$payload->{'composer.json'} = $config->rawData();

		$this->output->writeln('<info>Sending [POST] request to '.$this->apiEndpoint . '/satis' . '</info>');

		/** @var Response $response */
		$response = Request::post($this->apiEndpoint . '/satis')
			->body($payload)
			->sendsJson()
			->send();

//		$this->dumpResponseDebug($response);
//		die();

		$this->output->writeln('<info>Responded with return code ' . $response->code . '</info>');

		if ($response->code != 200) {
			throw new InvalidStateException('Error', 255);
		}

		// success
		if ($rewrite) {
			// rewrite original composer.json with modified one
			// with occasionally modified 'repositories' section
			$this->output->writeln('<info>Overwriting content of ' . $this->composerJsonFile . '</info>');
			$newComposerJsonContent = $response->body->{'generated-files'}->{'composer.json'}->{'content'};
			// $newComposerJsonContent is pretty printed string
			FileSystem::write($this->composerJsonFile, $newComposerJsonContent);
		}

		$this->output->writeln('<info>Generating ' . $this->satisJsonFile . '</info>');
		$satisJsonContent = $response->body->{'generated-files'}->{'satis.json'}->{'content'};
		FileSystem::write($this->satisJsonFile, $satisJsonContent);
	}


	/**
	 * Sends a request for satis build
	 * @param bool $rewrite
	 */
	public function update($rewrite = false)
	{
		if (!is_file($this->composerJsonFile)) {
			throw new FileNotFoundException("File composer.json not found. ('{$this->composerJsonFile}')", 255);
		}

		// read the composer.json file and post it's content via REST API
		$config = $this->configurationReader->read($this->composerJsonFile);

		// create payload to send
		$payload = new \stdClass();
		$payload->{'name'} = Strings::webalize($config->name());
		$payload->{'composer.json'} = $config->rawData();

		/** @var Response $response */
		$response = Request::put($this->apiEndpoint . '/satis')
			->body($payload)
			->sendsJson()
			->send();

		if ($response->code != 200) {
			throw new InvalidStateException('Error', 255);
		}

		// success
		if ($rewrite) {
			// rewrite original composer.json with modified one
			// with occasionally modified 'repositories' section
			$newComposerJsonContent = $response->body->{'generated-files'}->{'composer.json'}->{'content'};
			// $newComposerJsonContent is pretty printed string
			FileSystem::write($this->composerJsonFile, $newComposerJsonContent);
		}
	}

}
