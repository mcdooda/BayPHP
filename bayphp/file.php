<?php
/// @cond
namespace bay
{
/// @endcond

/**
 * \brief Retrieves data about bayfiles files by sending requests to http://api.bayfiles.com/v1/account/
 *
 * Represents a Bayfiles file.
 * fileId and infoToken are required in order to get the others attributes,
 * except deleteToken and owner which are provided by the Account::getFiles()
 * method.
 */
class File
{
	private $fileId;      /**< file id */
	private $infoToken;   /**< info token */
	private $deleteToken; /**< delete token: the file cannot be deleted if is not given as an argument to the constructor */
	private $size;        /**< file size in octets */
	private $sha1;        /**< SHA1 hash code */
	private $name;        /**< file name */
	
	private $linksUrl;    /**< links url given by the upload page */
	private $downloadUrl; /**< download url */
	private $deleteUrl;   /**< delete url */
	
	private $owner;       /**< file owner */
	
	/**
	 * \brief File constructor
	 *
	 * \param fileId file id
	 * \param infoToken info token
	 * \param deleteToken the delete token is optional but the file cannot be deleted if it is not provided
	 * \param size file size
	 * \param sha1 SHA1 hash code
	 * \param name file name
	 * \param owner file owner
	 */
	public function __construct($fileId, $infoToken, $deleteToken = null, $size = null, $sha1 = null, $name = null, $owner = null)
	{
		$this->fileId = $fileId;
		$this->infoToken = $infoToken;
		$this->deleteToken = $deleteToken;
		$this->size = $size;
		$this->sha1 = $sha1;
		$this->name = $name;
		
		$this->linksUrl = null;
		$this->downloadUrl = null;
		$this->deleteUrl = null;
		
		$this->owner = null;
	}
	
	/**
	 * \brief setter for the urls returned by the upload page
	 *
	 * \param linksUrl links url
	 * \param downloadUrl download url
	 * \param deleteUrl delete url
	 */
	public function setUploadLinks($linksUrl, $downloadUrl, $deleteUrl)
	{
		$this->linksUrl = $linksUrl;
		$this->downloadUrl = $downloadUrl;
		$this->deleteUrl = $deleteUrl;
	}
	
	/* getters */
	
	/**
	 * \brief file id getter
	 */
	public function getFileId()
	{
		return $this->fileId;
	}
	
	/**
	 * \brief info token getter
	 */
	public function getInfoToken()
	{
		return $this->infoToken;
	}
	
	/**
	 * \brief owner getter (should be an Account instance)
	 */
	public function getOwner()
	{
		return $this->owner;
	}
	
	/* info requests */
	
	/**
	 * \brief delete token getter
	 */
	public function getDeleteToken()
	{
		return $this->deleteToken;
	}
	
	/**
	 * \brief sends a request to /file/info/(fileId)/(infoToken) if the size is not cached yet and returns it
	 */
	public function getSize()
	{
		return $this->getInfo('size');
	}
	
	/**
	 * \brief sends a request to /file/info/(fileId)/(infoToken) if the SHA1 hash is not cached yet and returns it
	 */
	public function getSha1()
	{
		return $this->getInfo('sha1');
	}
	
	/**
	 * \brief sends a request to /file/info/(fileId)/(infoToken) if the name is not cached yet and returns it
	 */
	public function getName()
	{
		return $this->getInfo('name');
	}
	
	/**
	 * \brief links url getter
	 */
	public function getLinksUrl()
	{
		return $this->linksUrl;
	}
	
	/**
	 * \brief download url getter
	 */
	public function getDownloadUrl()
	{
		return $this->downloadUrl;
	}
	
	/**
	 * \brief delete url getter
	 */
	public function getDeleteUrl()
	{
		return $this->deleteUrl;
	}
	
	/* delete requests */
	
	/**
	 * \brief deletes the file, throws a FileException if the delete token was not given in the constructor
	 */
	public function delete()
	{
		$this->deleteRequest();
	}
	
	/* private */
	
	/* request methods */
	
	/**
	 * \brief sends a request to /file/info/(fileId)/(infoToken) if needed (the attribute is null)
	 */
	private function getInfo($key)
	{
		if ($this->{$key} == null)
			$this->info();
			
		return $this->{$key};
	}
	
	/**
	 * \brief requests /file/info/(fileId)/(infoToken)
	 */
	private function infoRequest()
	{
		$url = '/file/info/'.rawurlencode($this->fileId).'/'.rawurlencode($this->infoTaken);
		$request = new Request($url, $this->owner);
		$request->send('FileException');
		
		$this->size = $request->getResponse('size');
		$this->sha1 = $request->getResponse('sha1');
		$this->name = $request->getResponse('filename');
	}
	
	/**
	 * \brief requests /file/delete/(fileId)/(deleteToken)
	 */
	private function deleteRequest()
	{
		if ($this->deleteToken == null)
			throw new FileException('delete token unknown: unable to delete file fileId='.$this->fileId.' ; infoToken='.$this->infoToken);
			
		$url = '/file/delete/'.rawurlencode($this->fileId).'/'.rawurlencode($this->deleteToken);
		$request = new Request($url, $this->owner);
		$request->send('FileException');
	}
	
}

/// @cond
}
/// @endcond
?>
