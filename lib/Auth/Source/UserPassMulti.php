<?php
/* Written by Palle Girgensohn <girgen@pingpong.net> */
class sspmod_multipwsource_Auth_Source_UserPassMulti extends sspmod_core_Auth_UserPassBase {
	private $sources;
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		if (!array_key_exists('sources', $config)) {
			throw new Exception('The required "sources" config option was not found');
		}

		$this->sources = $config['sources'];
	}

	protected function login($username, $password) {
		assert('is_string($username)');
		assert('is_string($password)');

		$last_error = NULL;

		foreach ($this->sources as $authId) {
			$as = SimpleSAML_Auth_Source::getById($authId);
			if ($as === NULL) {
				throw new Exception("Invalid authentication source: $authId");
			}            
			try {
				return $as->login($username, $password);
			} catch (SimpleSAML_Error_Error $e) {
				if ($e->getErrorCode() === 'WRONGUSERPASS') {
					SimpleSAML_Logger::debug("Failed one source, trying next");
				} else {
					$last_error = $e;
				}
			} catch (SimpleSAML_Error_AuthSource $e) {
				SimpleSAML_Logger::error("Could not connect to $aithId, trying next");
			}
		}

		if ($last_error != NULL) throw $last_error;

		throw new SimpleSAML_Error_Error('WRONGUSERPASS');
	}
}
?>
