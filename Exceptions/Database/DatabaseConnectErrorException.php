<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions\Database;

class DatabaseConnectErrorException extends DatabaseException {
	public $DBError; //Ref to database_error object
}
