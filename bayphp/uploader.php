<?php
/// @cond
namespace bay
{
/// @endcond

/**
 * \brief Class for uploading files
 */
class Uploader
{
	private $uploadUrl;   /**< upload url */
	private $progressUrl; /**< upload progress url */
	
	/**
	 * \brief Uploader constructor
	 */
	public function __construct()
	{
		$this->uploadUrl = null;
		$this->progressUrl = null;
	}
	
	/**
	 * \brief call it before send() in order to get the upload and progress urls
	 */
	public function prepare($owner = null)
	{
		$url = '/file/uploadUrl';
		$request = new Request($url, $owner);
		$request->send('FileException');
		
		$this->uploadUrl = $request->getResponse('uploadUrl');
		$this->progressUrl = $request->getResponse('progressUrl');
	}
	
	/**
	 * \brief upload url getter
	 */
	public function getUploadUrl()
	{
		return $this->uploadUrl;
	}
	
	/**
	 * \brief progress url getter
	 */
	public function getProgressUrl()
	{
		return $this->progressUrl;
	}
	
	/**
	 * \brief sends the file and returns it as a File instance
	 */
	public function send($filename, $owner = null)
	{
		if ($this->uploadUrl != null)
			$this->prepare($owner);
		
		$url = $this->uploadUrl;
		$request = new Request($url, $this->owner);
		$request->addUploadFile('file', $this->filename);
		$request->send('FileException');
		
		$response = $request->getResponse();
		
		$file = new File($response->fileId, $response->infoToken, $response->deleteToken, $response->size, $response->sha1, $filename, $owner);
		$file->setUploadLinks($response->linksUrl, $response->downloadUrl, $response->deleteUrl);
		return $file;
	}
}

/// @cond
}
/// @endcond
?>
