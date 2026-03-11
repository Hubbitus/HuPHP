<?php
/**
* Custom PHP-CS-Fixer configuration for HuPHP project.
*
* This configuration implements a custom PHPDoc formatting style that:
* - Aligns PHPDoc asterisks with the described entity's indentation
* - Preserves all internal content indentation (spaces, tabs, formatting)
* - Uses two-asterisk closing format
* - Skips single-line PHPDoc comments
*
* All standard PSR rules are disabled to avoid interference with legacy code.
* Only the custom HuPhpDocFixer and braces_position are enabled.
*
* @see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
**/

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
* Custom PHPDoc fixer for HuPHP project.
*
* Formats PHPDoc comments according to project standards:
*
* 1. **Indentation**: PHPDoc asterisks align with the described entity
*    - Top-level: no indent before asterisk
*    - Class members: same indent as the member (4 spaces, tab, etc.)
*
* 2. **Content preservation**: All content after asterisk is kept exactly as-is
*    - Internal indentation (code examples, structures) is preserved
*    - Tabs remain tabs, spaces remain spaces
*    - No normalization or trimming of content
*
* 3. **Closing format**: Always uses two asterisks before slash
*
* 4. **Single-line PHPDoc**: Skipped entirely (single-line format)
*
* @example Top-level PHPDoc:
*   Start with slash and two asterisks, end with two asterisks and slash
*
* @example Class member PHPDoc:
*   Indented with same spaces as class member
*
* @example Preserved internal formatting:
*   Code examples inside PHPDoc keep their indentation
**/
class HuPhpDocFixer implements FixerInterface {
	/**
	* {@inheritdoc}
	*
	* @return string Unique name for this fixer
	**/
	public function getName(): string {
		return 'HubbitusPHPDocStyle/phpdoc_style';
	}

	/**
	* {@inheritdoc}
	*
	* @return FixerDefinition Definition of what this fixer does
	**/
	public function getDefinition(): FixerDefinition {
		return new FixerDefinition(
			'PHPDoc must start with slash-two-asterisks and end with two-asterisks-slash. No space before asterisk.',
			[new VersionSpecification(null, null)]
		);
	}

	/**
	* {@inheritdoc}
	*
	* Checks if tokens contain PHPDoc comments that need fixing.
	*
	* @param Tokens $tokens The tokens to check
	* @return bool True if T_DOC_COMMENT is found
	**/
	public function isCandidate(Tokens $tokens): bool {
		return $tokens->isAnyTokenKindsFound([T_DOC_COMMENT]);
	}

	/**
	* {@inheritdoc}
	*
	* This fixer does not make risky changes.
	*
	* @return bool Always false
	**/
	public function isRisky(): bool {
		return false;
	}

	/**
	* {@inheritdoc}
	*
	* Low priority to run AFTER braces_position.
	* This ensures braces are formatted first, then we fix any PHPDoc they corrupt.
	*
	* @return int Priority value (lower = runs later)
	**/
	public function getPriority(): int {
		// Run AFTER braces_position to fix any PHPDoc it corrupts
		return -100;
	}

	/**
	* {@inheritdoc}
	*
	* @param \SplFileInfo $file File to check
	* @return bool Always true - supports all PHP files
	**/
	public function supports(\SplFileInfo $file): bool {
		return true;
	}

	/**
	* {@inheritdoc}
	*
	* Fixes PHPDoc comments by:
	* 1. Getting indentation from whitespace before PHPDoc
	* 2. Applying that indent to all lines before asterisk
	* 3. Keeping all content after asterisk unchanged
	* 4. Ensuring closing line uses two asterisks format
	*
	* @param \SplFileInfo $file File being fixed
	* @param Tokens $tokens All tokens in the file
	**/
	public function fix(\SplFileInfo $file, Tokens $tokens): void {
		foreach ($tokens as $index => $token) {
			if ($token->isGivenKind(T_DOC_COMMENT)) {
				$content = $token->getContent();

				// Skip single-line PHPDoc (no newlines inside)
				if (\strpos($content, PHP_EOL) === false) {
					continue;
				}

				// Get the indentation from the whitespace token BEFORE this PHPDoc
				$baseIndent = '';
				$prevIndex = $index - 1;
				while ($prevIndex >= 0) {
					$prevToken = $tokens[$prevIndex];
					if ($prevToken->isGivenKind(T_WHITESPACE)) {
						// Get the last line of the whitespace (after the last newline)
						$whitespace = $prevToken->getContent();
						$lines = \preg_split('#\r\n|\r|\n#', $whitespace);
						$baseIndent = \end($lines);
						break;
					} elseif ($prevToken->isGivenKind([T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO])) {
						// Reached open tag, use empty indent
						break;
					}
					$prevIndex--;
				}

				$lines = \preg_split('#\r\n|\r|\n#', $content);
				$newLines = [];

				foreach ($lines as $i => $line) {
					// First line: keep as is (already has correct indent)
					if ($i === 0) {
						$newLines[] = $line;
						continue;
					}

					// Middle lines: apply base indent, then asterisk, keep rest as is
					if ($i < \count($lines) - 1) {
						// Remove leading whitespace and asterisk, capture the rest
						if (\preg_match('#^(\s*)\*(.*)$#', $line, $matches)) {
							$rest = $matches[2];
							// Keep the rest exactly as is - do not modify content indentation!
							// Apply base indent, then asterisk, then the rest unchanged
							$line = $baseIndent . '*' . $rest;
						}
					}

					// Last line: apply base indent and ensure two-asterisk closing format
					if ($i === \count($lines) - 1) {
						// Remove leading whitespace and always use two asterisks
						if (\preg_match('#^(\s*)\*+/+$#', $line)) {
							$line = $baseIndent . '**/';
						}
					}

					$newLines[] = $line;
				}

				$newContent = \implode("\n", $newLines);

				if ($newContent !== $content) {
					$tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
				}
			}
		}
	}
}

// Find all PHP files in project directory, excluding vendor, node_modules, and build
// ignoreDotFiles(false) to include .php-cs-fixer.php and other dot files
$finder = PhpCsFixer\Finder::create()
	->ignoreDotFiles(false)
	->exclude('vendor')
	->exclude('node_modules')
	->exclude('build')
	->in(__DIR__);

// Configure PHP-CS-Fixer with custom rules
$config = new PhpCsFixer\Config();
return $config
	->registerCustomFixers([new HuPhpDocFixer()])
	->setRules([
		// Disable ALL default rules - we only want our custom PHPDoc fixer
		'@PSR12' => false,
		'@PhpCsFixer' => false,

		// Disable all PHPDoc rules - use our custom fixer instead
		// Standard rules would conflict with our custom formatting
		'phpdoc_indent' => false,
		'phpdoc_trim' => false,
		'phpdoc_align' => false,
		'phpdoc_to_comment' => false,
		'phpdoc_no_access' => false,
		'phpdoc_order' => false,
		'phpdoc_scalar' => false,
		'phpdoc_separation' => false,
		'phpdoc_single_line_var_spacing' => false,
		'phpdoc_summary' => false,
		'phpdoc_trim_consecutive_blank_line_separation' => false,
		'phpdoc_types' => false,
		'phpdoc_types_order' => false,
		'phpdoc_var_without_name' => false,
		'phpdoc_var_annotation_correct_order' => false,

		// Disable whitespace rules that could interfere with PHPDoc formatting
		'no_trailing_whitespace' => false,
		'no_trailing_whitespace_in_comment' => false,
		'blank_line_before_statement' => false,
		'linebreak_after_opening_tag' => false,

		// Enable whitespace cleanup rules
		'no_whitespace_in_blank_line' => true, // Remove trailing whitespace in blank lines

		// Enable indentation type - use tabs instead of spaces
		'indentation_type' => true, // Use configured indentation type (tab by default)

		// Enable braces positioning - keep braces on same line as declaration (non-deprecated rule)
		'braces_position' => [
			'control_structures_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line',
			'anonymous_functions_opening_brace' => 'same_line',
			'classes_opening_brace' => 'same_line',
			'anonymous_classes_opening_brace' => 'same_line',
		],

		// Enable control structure braces - require braces for all control structures
		'control_structure_braces' => true, // Require braces for all control structures (if, else, elseif, etc.)

		// Enable statement indentation - automatically indent content inside braces
		'statement_indentation' => true, // Indent statements inside braces (one level deeper than opening brace)

		// Enable class attributes separation - require blank lines between methods only
		'class_attributes_separation' => [
			'elements' => [
				'method' => 'one',      // One blank line between methods
				'property' => 'none',   // No blank lines between properties
				'const' => 'none',      // No blank lines between constants
			],
		],

		// Enable native function invocation - require backslash prefix for all global PHP functions
		'native_function_invocation' => [
			'include' => ['@all'],       // Include all global functions
			'scope' => 'all',            // Fix all function calls, not just in namespaces
			'strict' => true,            // Remove leading \ if not meant to have it
			'exclude' => [],             // Don't exclude any functions
		],

		// Enable only our custom PHPDoc fixer
		'HubbitusPHPDocStyle/phpdoc_style' => true,
	])
	->setIndent("\t")  // Use tabs for indentation
	->setLineEnding("\n")  // Use Unix line endings
	->setFinder($finder)
	->setUsingCache(true);
