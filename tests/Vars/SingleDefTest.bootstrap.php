<?php
/**
* Bootstrap for SingleDefTest - defines CONF() function
**/
function CONF(): object {
	return new class {
		private array $configs = [
			'Hubbitus\Tests\HuPHP\Vars\TestConfigClass' => ['config_arg1', 'config_arg2'],
		];

		public function getRaw(string $className, bool $flag): ?array {
			return $this->configs[$className] ?? null;
		}
	};
}
