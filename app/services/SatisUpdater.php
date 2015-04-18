<?php
namespace SatisUpdater\Services;

use Eloquent\Composer\Configuration\ConfigurationReader;
use Httpful\Request;
use Httpful\Response;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;


/**
 * Satis update script
 * this script posts a content of composer.json file to specified app maintaining composer repositories
  */
class SatisUpdater extends AbstractService
{
	/**
	 * Path to composer.json file
	 * @var string
	 */
	protected $composerJsonFile;

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
	 * @param string $composerJsonFile
	 */
	public function setComposerJsonFile($composerJsonFile)
	{
		$this->composerJsonFile = $composerJsonFile;
	}

	/**
	 * @param string $apiEndpoint
	 */
	public function setApiEndpoint($apiEndpoint)
	{
		$this->apiEndpoint = $apiEndpoint;
	}

	/**
	 * Sends a request for satis build
	 * @param bool $rewrite
	 */
	public function update($rewrite = false)
	{
		if (is_file($this->composerJsonFile)) {
			// read the composer.json file and post it's content via REST API
			$config = $this->configurationReader->read($this->composerJsonFile);

			// create payload to send
			$payload = new \stdClass();
			$payload->{'name'} = Strings::webalize($config->name());
			$payload->{'composer.json'} = $config->rawData();

			/** @var Response $response */
			$response = Request::post($this->apiEndpoint . '/repository')
				->body($payload)
				->sendsJson()
				->send();

			if ($response->code == 200) {
				// success
				if ($rewrite) {
					// rewrite original composer.json with modified one
					// with occasionally modified 'repositories' section
					$newComposerJsonContent = $response->body->{'generated-files'}->{'composer.json'}->{'content'};
					// $newComposerJsonContent is pretty printed string
					//print_r($newComposerJsonContent);
					FileSystem::write($this->composerJsonFile, $newComposerJsonContent);
				}
			}

		}
	}

}
