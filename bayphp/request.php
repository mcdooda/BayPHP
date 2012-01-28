<?php
/// @cond
namespace bay
{
/// @endcond

/**
 * \brief Sends http requests to the Bayfiles API.
 */
class Request
{
	private static $baseUrl = 'http://api.bayfiles.com/v1'; /**< bayfiles api v1 */
	private $url;                                           /**< request url */
	private $response;                                      /**< decoded json response */
	private $method;                                        /**< http request method, the default is HTTP_METH_GET */
	private $uploadFiles;                                   /**< files to upload */
	
	/**
	 * \brief Request constructor
	 *
	 * \param url the request url, without the base url nor the ?session=xxx suffix
	 * \param user if provided, the request is sent using a specific account
	 */
	public function __construct($url, $user = null)
	{
		$this->url = $url;
		
		if ($user instanceof Account)
			$this->url .= '?session='.$user->getSession();
			
		else // session string
			$this->url .= '?session='.$user;
		
		$this->response = null;
		$this->method = HTTP_METH_GET;
		$this->uploadFiles = array();
	}
	
	/**
	 * \brief sets the http request method
	 *
	 * \param method http method, should be HTTP_METH_GET or HTTP_METH_POST
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}
	
	/**
	 * \brief adds a file to upload when calling send() method
	 *
	 * \param fieldname 
	 * \param filename name of the local file
	 */
	public function addUploadFile($fieldname, $filename)
	{
		$this->uploadFiles[$fieldname] = $filename;
	}
	
	/**
	 * \brief sends the request
	 *
	 * \param errorType if given, any error throws an exception of the given type, returns the error message instead
	 * \return the error message or an empty string if errorType is null
	 */
	public function send($errorType = null)
	{
		$url = $this->getUrl();
		$method = $this->method;
		$request = new \HttpRequest($url, $method);
		if ($this->method == HTTP_METH_POST)
		{
			foreach ($this->uploadFiles as $fieldname => $filename)
				$request->addPostFile($fieldname, $filename);
		}
		$request->send();
		$jsonResponse = $request->getResponseBody();
		$this->response = json_decode($jsonResponse);
		
		if ($errorType != null && $this->hasError())
		{
			$className = 'bay\\'.$errorType;
			throw new $className($this->getError());
		}
		else
			return $this->getError();
	}
	
	/**
	 * \brief returns whether the request failed
	 *
	 * \return a boolean meaning if the error message was empty or not after the request
	 */
	public function hasError()
	{
		return $this->response != null && $this->response->error != '';
	}
	
	/**
	 * \brief returns the error message given by the error field of the json response
	 *
	 * \return a string containing the error message
	 */
	public function getError()
	{
		return $this->response != null ? $this->response->error : '';
	}
	
	/**
	 * \brief the parsed json response
	 *
	 * \return an stdClass object read from the json string
	 */
	public function getResponse($key = null)
	{
		return $key != null ? $this->response->{$key} : $this->response;
	}
	
	/**
	 * \brief the request url
	 *
	 * \return the request url composed of the base url (http://api.bayfiles.com/v1), the request and optionaly a ?session=xxx suffix
	 */
	public function getUrl()
	{
		return self::$baseUrl.$this->url;
	}
	
}

/// @cond
}
/// @endcond
?>
