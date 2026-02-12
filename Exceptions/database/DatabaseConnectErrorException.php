<?php
declare(strict_types=1);

class DatabaseConnectErrorException extends DatabaseException {
	public $DBError; //Ref to database_error object
}
