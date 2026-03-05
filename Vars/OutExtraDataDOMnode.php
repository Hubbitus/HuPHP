<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\Debug\Dump;

/**
* Debug and backtrace toolkit.
*
* Class to provide easy wrapper to dump DOMElement (and DOMnode possibly), which default dump seems like:
* object(DOMElement)#97 (0) {
* }
* This wrapper put it into DOMDocument, and output it as formatted XML. For output standard family of dump::* methods used.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2009-05-20 19:10
**/
class OutExtraDataDOMnode extends OutExtraDataCommon {
	/** @var \DOMDocument|null */
	protected mixed $_var = null;

	/**
	* Constructor.
	*
	* @param \DOMNode $var Var to output with provided format.
	* @param string $encoding Format how output $var. Must contain 3 elements:
	*   `OutputType::CONSOLE`, `OutputType::WEB`, `OutputType::FILE` each represent according to
	*   format (See class {@see HuFormat} for more details).
	**/
	public function  __construct(\DOMNode $var, $encoding = 'utf-8'){
		$this->_var = new \DOMDocument('1.0', $encoding); // DOMDocument NEEDED to import into it nodes, it also NEEDED to export result asXML...
		$this->_var->appendChild($this->_var->importNode($var, true));
		$this->_var->preserveWhiteSpace = false;
		$this->_var->formatOutput = true;
		parent::__construct($this->_var);
	}

	public function strForConsole(array|string|null $format = null): string {
		return Dump::c(trim($this->_var->saveXML()), null, true);
	}

	public function strForFile(array|string|null $format = null): string {
		return Dump::log(trim($this->_var->saveXML()), '', true);
	}

	public function strForWeb(array|string|null $format = null): string {
		return Dump::w(trim($this->_var->saveXML()), '', true);
	}
}
