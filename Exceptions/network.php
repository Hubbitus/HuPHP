<?
include_once('Exceptions/BaseException.php');

class NetworkException extends Exception{}

class SocketOpenException extends NetworkException{}
class SocketReadException extends NetworkException{}
class SocketReadTimeoutException extends SocketReadException{}
class SocketWriteException extends NetworkException{}
class SocketWriteTimeoutException extends SocketWriteException{}
?>