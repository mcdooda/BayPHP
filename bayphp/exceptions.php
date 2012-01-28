<?php
/// @cond
namespace bay
{
/// @endcond

/**
 * \brief base exception class
 *
 * These exceptions contain error message given by Bayfiles most of the time.
 * They may be thrown by a wrong usage of BayPHP, for example if you try to
 * delete a file without its delete token, the request is not even attempted.
 */
class Exception extends \Exception
{
	/**
	 * \brief standard serializer method
	 */
	public function __toString()
	{
		return get_class($this).': '.$this->message;
	}
}

/**
 * \brief AccountException occurs when requesting /account fails.
 */
class AccountException extends Exception
{

}

/**
 * \brief FileException occurs when requesting /file fails.
 */
class FileException extends Exception
{

}

/// @cond
}
/// @endcond
?>
