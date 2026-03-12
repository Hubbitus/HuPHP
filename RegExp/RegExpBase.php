<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\RegExp;

use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Vars\HuClass;

/**
* RegExp manipulation.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2008-05-29
**/
abstract class RegExpBase extends HuClass implements IRegExp {
	protected ?string $sourceText = null;
	protected string|array|null $regExp = null;
	protected int $matchCount = 0;
	protected ?array $matches = null;
	protected bool $matchesValid = false;
	protected string|array|null $replaceTo = null;
	protected mixed $replaceRes;
	protected bool $replaceValid = false;

	/** @var array<string, string> */
	public array $paireddelimeters = [
		'{' => '}',
		'<' => '>',
		'(' => ')',
		'[' => ']',
	];

	/**
	* Constructor.
	*
	* For parameters {@see ->set()}
	**/
	public function __construct($regexp = null, $text = null, $replaceTo = null) {
		$this->set($regexp, $text, $replaceTo);
	}

	/**
	* Return N-th single match
	*
	* @param int $Number Number of interesting match
	**/
	public function match(int $Number): string|array {
		if (!$this->matchesValid) {
			$this->doMatch();
		}

		return $this->matches[$Number];
	}

	/**
	* Return regexp string.
	**/
	public function getRegExp(): ?string {
		return $this->regExp;
	}

	/**
	* Set RegExp from string.
	*
	* @param string|array $regexp
	* @throws VariableIsNullException
	**/
	public function &setRegExp($regexp): static {
		$this->regExp = Vars::requiredNotNull($regexp);
		$this->matchesValid = false;
		return $this;
	}

	/**
	* Return current text.
	**/
	public function getText(): ?string {
		return $this->sourceText;
	}

	/**
	* Set text to match from string.
	*
	* @param string $text
	**/
	public function &setText($text): static {
		$this->sourceText = Vars::requiredNotNull($text);
		$this->matchesValid = false;
		return $this;
	}

	/**
	* Equivalent of {@see ->&setText()}, but assign text by ref. Be very carefully!
	*
	* @param string $text
	**/
	public function &setTextRef(&$text): static {
		$this->sourceText =& $text;
		$this->matchesValid = false;
		return $this;
	}

	/**
	* Set ReplaceTo
	*
	* @param string|array $text
	**/
	public function &setReplaceTo($text): static {
		$this->replaceTo = Vars::requiredNotNull($text);
		$this->replaceValid = $this->matchesValid = false;
		return $this;
	}

	/**
	* Return count of matches. If matches not valid - by default do ::doMatchAll() first
	**/
	public function matchCount(): int {
		if (!$this->matchesValid) {
			$this->doMatchAll();
		}
		return $this->matchCount;
	}

	/**
	* Set Pattern, text, replacement. Shorthand to appropriate methods.
	*
	* @param string|array|null $RegExp
	* @param string|null $Text
	* @param string|array|null $ReplaceTo
	**/
	public function &set($RegExp = null, $Text = null, $ReplaceTo = null): static {
		if ($RegExp !== null) {
			$this->setRegExp($RegExp);
		}
		if ($Text !== null) {
			$this->setText($Text);
		}
		if ($ReplaceTo !== null) {
			$this->setReplaceTo($ReplaceTo);
		}
		return $this;
	}

	/**
	* Do test, faster then doMatch, don't filling ->matches, ->matchCount and other.
	**/
	abstract public function test();

	/**
	* Description of $flags and $offset see on http://www.php.net/preg_match_all
	* Called by default, in ->match()!
	**/
	abstract public function &doMatch($flags = null, $offset = null);

	/**
	* {@see ->doMatch()}. But match all occurrences.
	**/
	abstract public function &doMatchAll($flags = null, $offset = null);

	/**
	* Return startDelimiter
	*
	* @param ?int $item If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	**/
	public function getRegExpDelimiterStart($item = null): string {
		$item ??= 0;
		if (\is_array($this->regExp)) {
			return $this->regExp[$item][0];
		}
		else {
			return $this->regExp[0];
		}
	}

	/**
	* Return endDelimiter
	*
	* @param ?int $item If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	**/
	public function getRegExpDelimiterEnd($item = null): string {
		$startDelimiter = $this->getRegExpDelimiterStart($item);
		if (isset($this->paireddelimeters[$startDelimiter])) {
			return $this->paireddelimeters[$startDelimiter];
		}
		else {
			return $startDelimiter;
		}
	}

	/**
	* Assume RegExp correct. Do not check it.
	*
	* @param ?int $item If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	**/
	public function getRegExpBody($item = null): string {
		$item ??= 0;
		$regexp = \is_array($this->regExp) ? $this->regExp[$item] : $this->regExp;
		$endPos = \strrpos($regexp, $this->getRegExpDelimiterEnd($item));
		return \substr($regexp, 1, $endPos - 1);
	}

	/**
	* Return RegExpModifiers
	*
	* @param ?int $item If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	**/
	public function getRegExpModifiers($item = null): string {
		$item ??= 0;
		$regexp = \is_array($this->regExp) ? $this->regExp[$item] : $this->regExp;
		$endPos = \strrpos($regexp, $this->getRegExpDelimiterEnd($item));
		return \substr($regexp, $endPos + 1);
	}

	/**
	* Description see {@link http://php.net/preg_replace}
	*
	* @param int $limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @return mixed Replaced value.
	**/
	abstract public function replace($limit = -1);

	/**
	* Split by regexp. Results as usual in matches.
	*
	* @since Version 2.1.1
	*
	* @param int $limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @param ?int $flags Flags for the operation.
	**/
	abstract public function &split($limit = -1, $flags = null): static;

	/**
	* Full(os sub, if $n present) array of matches after call (not checked!) {@see doMatch()}, {@see doMatchAll()}, {@see split()}
	*
	* @param int|null $n Number of sub array
	**/
	public function getMatches(?int $n = null): ?array {
		return ($n !== null ? ($this->matches[$n] ?? null) : $this->matches);
	}

	/**
	* Full equivalent of {@see getMatches()) except of result returned as Object(HuArray) instead of regular array.
	*
	* @param ?int $n Directly passed to {@see getMatches}
	**/
	public function getHuMatches($n = null): HuArray {
		return new HuArray($this->getMatches($n));
	}
}
