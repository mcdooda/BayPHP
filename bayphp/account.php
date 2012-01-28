<?php
/// @cond
namespace bay
{
/// @endcond

/**
 * \brief Retrieves data about bayfiles accounts by sending requests to http://api.bayfiles.com/v1/account/
 */
class Account
{
	private $username;   /**< user name */
	private $password;   /**< pass word */
	private $session;    /**< session string given by /account/login */
	
	private $email;      /**< email */
	private $filesCount; /**< number of files */
	private $storage;    /**< storage */
	private $premium;    /**< premium */
	private $expires;    /**< session expiration unix timestamp */
	private $codes;      /**< number of codes */
	
	private $files;      /**< files owned by this account */
	
	/**
	 * \brief Account construct
	 *
	 * \param username username
	 * \param password password
	 */
	public function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
		$this->session = null;
		
		$this->email = null;
		$this->filesCount = null;
		$this->storage = null;
		$this->premium = null;
		$this->expires = null;
		$this->codes = null;
		
		$this->files = array();
	}
	
	/* getters */
	
	/**
	 * \brief user name getter
	 *
	 * \return user name
	 */
	public function getUsername()
	{
		return $this->username;
	}
	
	/**
	 * \brief password getter
	 *
	 * \return password
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/* info requests */
	
	/**
	 * \brief sends a request to /account/info if the session string is not cached yet and returns it
	 *
	 * \return session string
	 */
	public function getSession()
	{
		return $this->getInfo('session');
	}

	/**
	 * \brief sends a request to /account/info if the email is not cached yet and returns it
	 *
	 * \return email
	 */	
	public function getEmail()
	{
		return $this->getInfo('email');
	}
	
	/**
	 * \brief sends a request to /account/info if the files count is not cached yet and returns it
	 */
	public function getFilesCount()
	{
		return $this->getInfo('filesCount');
	}
	
	/**
	 * \brief sends a request to /account/info if the session string is not cached yet and returns it
	 *
	 * \return storage
	 */
	public function getStorage()
	{
		return $this->getInfo('storage');
	}
	
	/**
	 * \brief sends a request to /account/info if the premiumness is not cached yet and returns it
	 */
	public function isPremium()
	{
		return $this->getInfo('premium');
	}
	
	/**
	 * \brief sends a request to /account/info if the session string is not cached yet and returns it
	 */
	public function getExpires()
	{
		return $this->getInfo('expires');
	}
	
	/**
	 * \brief sends a request to /account/info if the number of codes is not cached yet and returns it
	 */
	public function getCodes()
	{
		return $this->getInfo('codes');
	}
	
	/* file requests */
	
	/**
	 * \brief sends a request to /account/files if this request has not been done yet.
	 *
	 * \return array of bay\\File(s)
	 */
	public function getFiles()
	{
		if (count($this->files) == 0)
			$this->filesRequest();
			
		return $this->files;
	}
	
	/* edit requests */
	
	/**
	 * \brief sends a request to /account/edit/password/(password) to edit the password
	 */
	public function editPassword($value)
	{
		return $this->editRequest('password', $value);
	}
	
	/**
	 * \brief sends a request to /account/edit/email/(email) to edit the email
	 */
	public function editEmail($value)
	{
		return $this->editRequest('email', $value);
	}
	
	/* login and logout requests */
	
	/**
	 * \brief sends a request to /account/login/(username)/(password)
	 */
	public function login()
	{
		$this->loginRequest();
	}
	
	/**
	 * \brief sends a request to /account/logout
	 */
	public function logout()
	{
		$this->logoutRequest();
	}
	
	/* private */
	
	/* request methods */
	
	private function getInfo($key)
	{
		if ($this->{$key} == null)
			$this->infoRequest();
			
		return $this->{$key};
	}
	
	/**
	 * \brief requests /account/login/<username>/<password>
	 */
	private function loginRequest()
	{
		$url = '/account/login/'.rawurlencode($this->username).'/'.rawurlencode($this->password);
		$request = new Request($url);
		$request->send('AccountException');
		
		$this->session = $request->getResponse('session');
	}
	
	/**
	 * \brief requests /account/logout
	 */
	private function logoutRequest()
	{
		$url = '/account/logout';
		$request = new Request($url, $this);
		$request->send('AccountException');
	}
	
	/**
	 * \brief requests /account/info
	 */
	private function infoRequest()
	{
		if ($this->session == null)
			$this->loginRequest();
			
		$url = '/account/info';
		$request = new Request($url, $this);
		$request->send('AccountException');
		
		$this->email = $request->getResponse('email');
		$this->filesCount = $request->getResponse('files');
		$this->storage = $request->getResponse('storage');
		$this->premium = (bool)$request->getResponse('premium');
		$this->codes = $request->getResponse('codes');
	}
	
	/**
	 * \brief requests /account/edit
	 */
	private function editRequest($key, $value)
	{
		if ($this->session == null)
			$this->loginRequest();
			
		$url = '/account/edit/'.rawurlencode($key).'/'.rawurlencode($value);
		$request = new Request($url, $this);
		$request->send('AccountException');
		
		$this->email = $request->getResponse('email');
		$this->filesCount = $request->getResponse('files');
		$this->storage = $request->getResponse('storage');
		$this->premium = (bool)$request->getResponse('premium');
		$this->codes = $request->getResponse('codes');
	}
	
	/**
	 * \brief requests /account/files
	 */
	private function filesRequest()
	{
		if ($this->session == null)
			$this->loginRequest();
		
		$url = '/account/files';
		$request = new Request($url, $this);
		$request->send('AccountException');
		
		foreach ($request->getResponse() as $fileId => $file)
		{
			if ($fileId == "error") continue;
			
			$this->files[] = new File($fileId, $file->infoToken, $file->deleteToken, $file->size, $file->sha1, $file->filename, $this);
		}	
	}
	
}

/// @cond
}
/// @endcond
?>
