<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

if(!defined('XAJAX_DEFAULT_CHAR_ENCODING')) DEFINE('XAJAX_DEFAULT_CHAR_ENCODING', 'utf-8');
if(!defined('XAJAX_PROCESSING_EVENT')) DEFINE('XAJAX_PROCESSING_EVENT', 'xajax processing event');
if(!defined('XAJAX_PROCESSING_EVENT_BEFORE')) DEFINE('XAJAX_PROCESSING_EVENT_BEFORE', 'beforeProcessing');
if(!defined('XAJAX_PROCESSING_EVENT_AFTER')) DEFINE('XAJAX_PROCESSING_EVENT_AFTER', 'afterProcessing');
if(!defined('XAJAX_PROCESSING_EVENT_INVALID')) DEFINE('XAJAX_PROCESSING_EVENT_INVALID', 'invalidRequest');

class KI_AJAX
{
	public $aSettings;
	public $bErrorHandler;
	public $aProcessingEvents;
	public $bExitAllowed;
	public $bCleanBuffer;
	public $sLogFile;
	public $sCoreIncludeOutput;
	public $objPluginManager;
	public $objArgumentManager;
	public $objResponseManager;
	public $objLanguageManager;
	public $sLocalFolder;
	
	function KI_AJAX($sLocalFolder = '', $sRequestURI = NULL, $sLanguage = NULL)
	{
		$this->bErrorHandler = FALSE;
		$this->aProcessingEvents = array();
		$this->bExitAllowed = TRUE;
		$this->bCleanBuffer = TRUE;
		$this->sLogFile = '';
		 
		$this->__wakeup();
		
		$this->configureMany(
			array(
				'characterEncoding' => XAJAX_DEFAULT_CHAR_ENCODING,
				'decodeUTF8Input' => FALSE,
				'outputEntities' => FALSE,
				'defaultMode' => 'asynchronous',
				'defaultMethod' => 'POST',	// W3C: Method is case sensitive
				'wrapperPrefix' => 'xajax_',
				'debug' => FALSE,
				'verbose' => FALSE,
				'useUncompressedScripts' => FALSE,
				'statusMessages' => FALSE,
				'waitCursor' => TRUE,
				'scriptDeferral' => FALSE,
				'exitAllowed' => TRUE,
				'errorHandler' => FALSE,
				'cleanBuffer' => FALSE,
				'allowBlankResponse' => FALSE,
				'allowAllResponseTypes' => FALSE,
				'generateStubs' => TRUE,
				'logFile' => '',
				'timeout' => 6000,
				'version' => $this->getVersion(),
				'localFolder' => $sLocalFolder
				)
			);

		if(null !== $sRequestURI) $this->configure('requestURI', $sRequestURI);
		else $this->configure('requestURI', $this->_detectURI());
		
		if(null !== $sLanguage) $this->configure('language', $sLanguage);

		if('utf-8' != XAJAX_DEFAULT_CHAR_ENCODING) $this->configure("decodeUTF8Input", TRUE);
	}
	
	function __sleep()
	{
		$aMembers = get_class_vars(get_class($this));
		
		if(isset($aMembers['objLanguageManager'])) unset($aMembers['objLanguageManager']);
		if(isset($aMembers['objPluginManager'])) unset($aMembers['objPluginManager']);
		if(isset($aMembers['objArgumentManager'])) unset($aMembers['objArgumentManager']);
		if(isset($aMembers['objResponseManager'])) unset($aMembers['objResponseManager']);
		if(isset($aMembers['sCoreIncludeOutput'])) unset($aMembers['sCoreIncludeOutput']);
		
		return array_keys($aMembers);
	}
	
	function __wakeup()
	{
		ob_start();

		require $this->sLocalFolder.'/manager/plugin.php';
		require $this->sLocalFolder.'/manager/language.php';
		require $this->sLocalFolder.'/manager/argument.php';
		require $this->sLocalFolder.'/manager/response.php';
		require $this->sLocalFolder.'/request.php';
		require $this->sLocalFolder.'/response.php';

		$aPluginFolders = array();
		$aPluginFolders[] = '../'.$this->sLocalFolder.'/plugins';
		$aPluginFolders[] = $this->sLocalFolder.'/plugin_layer';
		
		$this->objPluginManager =& xajaxPluginManager::getInstance();
		$this->objPluginManager->loadPlugins($aPluginFolders);

		$this->objLanguageManager =& xajaxLanguageManager::getInstance();
		$this->objArgumentManager =& xajaxArgumentManager::getInstance();
		$this->objResponseManager =& xajaxResponseManager::getInstance();
		
		$this->sCoreIncludeOutput = ob_get_clean();
	}

	function &getGlobalResponse()
	{
		static $obj;
		
		if(!$obj) $obj = new xajaxResponse();
	
		return $obj;
	}

	function getVersion()
	{
		return 'xajax 0.5';
	}

	function register($sType, $mArg)
	{
		$aArgs = func_get_args();
		$nArgs = func_num_args();

		if(2 < $nArgs)
		{
			if(XAJAX_PROCESSING_EVENT == $aArgs[0])
			{
				$sEvent = $aArgs[1];
				$xuf =& $aArgs[2];

				if(FALSE == is_a($xuf, 'xajaxUserFunction')) $xuf =& new xajaxUserFunction($xuf);

				$this->aProcessingEvents[$sEvent] =& $xuf;
				return TRUE;
			}
		}
		
		if(1 < $nArgs) $aArgs[1] =& $mArg;

		return $this->objPluginManager->register($aArgs);
	}

	function configure($sName, $mValue)
	{
		if('errorHandler' == $sName)
		{
			if(TRUE === $mValue || FALSE === $mValue) $this->bErrorHandler = $mValue;
		}
		else if('exitAllowed' == $sName)
		{
			if(TRUE === $mValue || FALSE === $mValue) $this->bExitAllowed = $mValue;
		}
		else if('cleanBuffer' == $sName)
		{
			if(TRUE === $mValue || FALSE === $mValue) $this->bCleanBuffer = $mValue;
		}
		else if('logFile' == $sName)
		{
			$this->sLogFile = $mValue;
		}
		else if('localFolder' == $sName)
		{
			$this->sLocalFolder = $mValue;
		}
		
		$this->objLanguageManager->configure($sName, $mValue);
		$this->objArgumentManager->configure($sName, $mValue);
		$this->objPluginManager->configure($sName, $mValue);
		$this->objResponseManager->configure($sName, $mValue);

		$this->aSettings[$sName] = $mValue;
	}

	function configureMany($aOptions)
	{
		foreach($aOptions as $sName => $mValue) $this->configure($sName, $mValue);
	}

	function getConfiguration($sName)
	{
		if(isset($this->aSettings[$sName])) return $this->aSettings[$sName];
		
		return NULL;
	}

	function canProcessRequest()
	{
		return $this->objPluginManager->canProcessRequest();
	}

	function processRequest()
	{
		if(headers_sent($filename, $linenumber))
		{
			echo "Output has already been sent to the browser at {$filename}:{$linenumber}.\n";
			echo 'Please make sure the command $xajax->processRequest() is placed before this.';
			exit();
		}

		if($this->canProcessRequest())
		{
			if($this->bErrorHandler)
			{
				$GLOBALS['xajaxErrorHandlerText'] = "";
				set_error_handler("xajaxErrorHandler");
			}
			
			$mResult = TRUE;

			if(isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]))
			{
				$bEndRequest = FALSE;
				$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]->call(array(&$bEndRequest));
				$mResult = (FALSE === $bEndRequest);
			}

			if(TRUE === $mResult) $mResult = $this->objPluginManager->processRequest();

			if(TRUE === $mResult)
			{
				if($this->bCleanBuffer)
				{
					$er = error_reporting(0);
					
					while(ob_get_level() > 0) ob_end_clean();
					
					error_reporting($er);
				}

				if(isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]))
				{
					$bEndRequest = FALSE;
					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]->call(array(&$bEndRequest));
					
					if(TRUE === $bEndRequest)
					{
						$this->objResponseManager->clear();
						$this->objResponseManager->append($aResult[1]);
					}
				}
			}
			else if(is_string($mResult))
			{
				if($this->bCleanBuffer)
				{
					$er = error_reporting(0);
					
					while(ob_get_level() > 0) ob_end_clean();
					
					error_reporting($er);
				}
				
				$this->objResponseManager->clear();
				$this->objResponseManager->append(new xajaxResponse());

				if(isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID])) $this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]->call();
				else $this->objResponseManager->debug($mResult);
			}

			if($this->bErrorHandler)
			{
				$sErrorMessage = $GLOBALS['xajaxErrorHandlerText'];
				
				if(!empty($sErrorMessage))
				{
					if(0 < strlen($this->sLogFile))
					{
						$fH = @fopen($this->sLogFile, "a");
						
						if(NULL != $fH)
						{
							fwrite(
								$fH, 
								$this->objLanguageManager->getText('LOGHDR:01')
								.strftime("%b %e %Y %I:%M:%S %p") 
								.$this->objLanguageManager->getText('LOGHDR:02')
								.$sErrorMessage 
								.$this->objLanguageManager->getText('LOGHDR:03')
								);
							fclose($fH);
						}
						else
						{
							$this->objResponseManager->debug($this->objLanguageManager->getText('LOGERR:01').$this->sLogFile);
						}
					}
					
					$this->objResponseManager->debug($this->objLanguageManager->getText('LOGMSG:01') .$sErrorMessage);
				}
			}

			$this->objResponseManager->send();

			if($this->bErrorHandler) restore_error_handler();
			if($this->bExitAllowed) exit();
		}
	}

	function printJavascript($sJsURI = "", $aJsFiles = array())
	{
		if(0 < strlen($sJsURI)) $this->configure("javascript URI", $sJsURI);
		if(0 < count($aJsFiles)) $this->configure("javascript files", $aJsFiles);

		$this->objPluginManager->generateClientScript();
	}

	function getJavascript($sJsURI = '', $aJsFiles = array())
	{
		ob_start();
		$this->printJavascript($sJsURI, $aJsFiles);
		return ob_get_clean();
	}

	function autoCompressJavascript($sJsFullFilename = NULL, $bAlways = FALSE)
	{
		$sJsFile = 'xajax_js/xajax_core.js';

		if ($sJsFullFilename) {
			$realJsFile = $sJsFullFilename;
		}
		else {
			$realPath = realpath(dirname(dirname(__FILE__)));
			$realJsFile = $realPath . '/'. $sJsFile;
		}

		// Create a compressed file if necessary
		if (!file_exists($realJsFile) || TRUE == $bAlways) {
			$srcFile = str_replace('.js', '_uncompressed.js', $realJsFile);
			if (!file_exists($srcFile)) {
				trigger_error(
					$this->objLanguageManager->getText('CMPRSJS:RDERR:01') 
					. dirname($realJsFile) 
					. $this->objLanguageManager->getText('CMPRSJS:RDERR:02')
					, E_USER_ERROR
					);
			}
			require_once(dirname(__FILE__) . '/xajaxCompress.inc.php');
			$javaScript = implode('', file($srcFile));
			$compressedScript = xajaxCompressFile($javaScript);
			$fH = @fopen($realJsFile, 'w');
			if (!$fH) {
				trigger_error(
					$this->objLanguageManager->getText('CMPRSJS:WTERR:01') 
					. dirname($realJsFile) 
					. $this->objLanguageManager->getText('CMPRSJS:WTERR:02')
					, E_USER_ERROR
					);
			}
			else {
				fwrite($fH, $compressedScript);
				fclose($fH);
			}
		}
	}
	
	function _compressSelf($sFolder=null)
	{
		if (null == $sFolder)
			$sFolder = dirname(dirname(__FILE__));
			
		require_once(dirname(__FILE__) . '/xajaxCompress.inc.php');

		if ($handle = opendir($sFolder)) {
			while (!(FALSE === ($sName = readdir($handle)))) {
				if ('.' != $sName && '..' != $sName && is_dir($sFolder . '/' . $sName)) {
					$this->_compressSelf($sFolder . '/' . $sName);
				} else if (8 < strlen($sName) && 0 == strpos($sName, '.compressed')) {
					if ('.inc.php' == substr($sName, strlen($sName) - 8, 8)) {
						$sName = substr($sName, 0, strlen($sName) - 8);
						$sPath = $sFolder . '/' . $sName . '.inc.php';
						if (file_exists($sPath)) {
							
							$aParsed = array();
							$aFile = file($sPath);
							$nSkip = 0;
							foreach (array_keys($aFile) as $sKey)
								if ('//SkipDebug' == $aFile[$sKey])
									++$nSkip;
								else if ('//EndSkipDebug' == $aFile[$sKey])
									--$nSkip;
								else if (0 == $nSkip)
									$aParsed[] = $aFile[$sKey];
							unset($aFile);
							
							$compressedScript = xajaxCompressFile(implode('', $aParsed));
							
							$sNewPath = $sPath;
							$fH = @fopen($sNewPath, 'w');
							if (!$fH) {
								trigger_error(
									$this->objLanguageManager->getText('CMPRSPHP:WTERR:01') 
									. $sNewPath 
									. $this->objLanguageManager->getText('CMPRSPHP:WTERR:02')
									, E_USER_ERROR
									);
							}
							else {
								fwrite($fH, $compressedScript);
								fclose($fH);
							}
						}
					}
				}
			}
			
			closedir($handle);
		}
	}
	
	function _compile($sFolder = NULL, $bWriteFile = TRUE)
	{
		if (null == $sFolder)
			$sFolder = dirname(__FILE__);
			
		require_once(dirname(__FILE__) . '/xajaxCompress.inc.php');
		
		$aOutput = array();

		if($handle = opendir($sFolder))
		{
			while(!(FALSE === ($sName = readdir($handle))))
			{
				if('.' != $sName && '..' != $sName && is_dir($sFolder . '/' . $sName))
				{
					$aOutput[] = $this->_compile($sFolder . '/' . $sName, FALSE);
				}
				else if(8 < strlen($sName))
				{
					if('.inc.php' == substr($sName, strlen($sName) - 8, 8))
					{
						$sName = substr($sName, 0, strlen($sName) - 8);
						$sPath = $sFolder . '/' . $sName . '.inc.php';
						
						if (
							'xajaxAIO' != $sName && 
							'legacy' != $sName && 
							'xajaxCompress' != $sName
							) {
							if (file_exists($sPath)) {
								
								$aParsed = array();
								$aFile = file($sPath);
								$nSkip = 0;
								foreach (array_keys($aFile) as $sKey)
									if ('//SkipDebug' == substr($aFile[$sKey], 0, 11))
										++$nSkip;
									else if ('//EndSkipDebug' == substr($aFile[$sKey], 0, 14))
										--$nSkip;
									else if ('//SkipAIO' == substr($aFile[$sKey], 0, 9))
										++$nSkip;
									else if ('//EndSkipAIO' == substr($aFile[$sKey], 0, 12))
										--$nSkip;
									else if ('<'.'?php' == substr($aFile[$sKey], 0, 5)) {}
									else if ('?'.'>' == substr($aFile[$sKey], 0, 2)) {}
									else if (0 == $nSkip)
										$aParsed[] = $aFile[$sKey];
								unset($aFile);
								
								$aOutput[] = xajaxCompressFile(implode('', $aParsed));
							}
						}
					}
				}
			}
			
			closedir($handle);
		}
		
		if ($bWriteFile)
		{
			$fH = @fopen($sFolder . '/xajaxAIO.inc.php', 'w');
			if (!$fH) {
				trigger_error(
					$this->objLanguageManager->getText('CMPRSAIO:WTERR:01') 
					. $sFolder 
					. $this->objLanguageManager->getText('CMPRSAIO:WTERR:02')
					, E_USER_ERROR
					);
			}
			else {
				fwrite($fH, '<'.'?php ');
				fwrite($fH, implode('', $aOutput));
				fclose($fH);
			}
		}
		
		return implode('', $aOutput);
	}

	function _detectURI()
	{
		$aURL = array();

		if(!empty($_SERVER['REQUEST_URI']))
		{
			$_SERVER['REQUEST_URI'] = str_replace(
				array('"',"'",'<','>'), 
				array('%22','%27','%3C','%3E'), 
				$_SERVER['REQUEST_URI']
				);
				
			$aURL = parse_url($_SERVER['REQUEST_URI']);
		}

		if(empty($aURL['scheme']))
		{
			if(!empty($_SERVER['HTTP_SCHEME']))
			{
				$aURL['scheme'] = $_SERVER['HTTP_SCHEME'];
			}
			else
			{
				$aURL['scheme'] = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') ? 'https' : 'http';
			}
		}

		if(empty($aURL['host']))
		{
			if(!empty($_SERVER['HTTP_X_FORWARDED_HOST']))
			{
				if(strpos($_SERVER['HTTP_X_FORWARDED_HOST'], ':') > 0)
				{
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_X_FORWARDED_HOST']);
				}
				else
				{
					$aURL['host'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
				}
			}
			else if(!empty($_SERVER['HTTP_HOST']))
			{
				if(strpos($_SERVER['HTTP_HOST'], ':') > 0)
				{
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_HOST']);
				}
				else
				{
					$aURL['host'] = $_SERVER['HTTP_HOST'];
				}
			}
			else if(!empty($_SERVER['SERVER_NAME']))
			{
				$aURL['host'] = $_SERVER['SERVER_NAME'];
			}
			else
			{
				echo $this->objLanguageManager->getText('DTCTURI:01');
				echo $this->objLanguageManager->getText('DTCTURI:02');
				exit();
			}
		}

		if(empty($aURL['port']) && !empty($_SERVER['SERVER_PORT']))
		{
			$aURL['port'] = $_SERVER['SERVER_PORT'];
		}

		if(!empty($aURL['path']))
			if(0 == strlen(basename($aURL['path'])))
				unset($aURL['path']);
		
		if(empty($aURL['path']))
		{
			$sPath = array();
			
			if(!empty($_SERVER['PATH_INFO']))
			{
				$sPath = parse_url($_SERVER['PATH_INFO']);
			}
			else
			{
				$sPath = parse_url($_SERVER['PHP_SELF']);
			}
			
			if(isset($sPath['path'])) $aURL['path'] = str_replace(array('"',"'",'<','>'), array('%22','%27','%3C','%3E'), $sPath['path']);
			
			unset($sPath);
		}

		if(empty($aURL['query']) && !empty($_SERVER['QUERY_STRING']))
		{
			$aURL['query'] = $_SERVER['QUERY_STRING'];
		}

		if(!empty($aURL['query']))
		{
			$aURL['query'] = '?'.$aURL['query'];
		}

		$sURL = $aURL['scheme'].'://';
		
		if(!empty($aURL['user']))
		{
			$sURL.= $aURL['user'];
			
			if(!empty($aURL['pass']))
			{
				$sURL.= ':'.$aURL['pass'];
			}
			
			$sURL.= '@';
		}

		$sURL.= $aURL['host'];

		if(!empty($aURL['port']) && (($aURL['scheme'] == 'http' && $aURL['port'] != 80) || ($aURL['scheme'] == 'https' && $aURL['port'] != 443)))
		{
			$sURL.= ':'.$aURL['port'];
		}

		$sURL.= $aURL['path'].@$aURL['query'];
		unset($aURL);
		$aURL = explode("?", $sURL);
		
		if(1 < count($aURL))
		{
			$aQueries = explode("&", $aURL[1]);

			foreach($aQueries as $sKey => $sQuery)
			{
				if("xjxGenerate" == substr($sQuery, 0, 11)) unset($aQueries[$sKey]);
			}
			
			$sQueries = implode("&", $aQueries);
			$aURL[1] = $sQueries;
			$sURL = implode("?", $aURL);
		}

		return $sURL;
	}

	function setCharEncoding($sEncoding)
	{
		$this->configure('characterEncoding', $sEncoding);
	}

	function getCharEncoding()
	{
		return $this->getConfiguration('characterEncoding');
	}

	function setFlags($flags)
	{
		foreach ($flags as $name => $value) {
			$this->configure($name, $value);
		}
	}

	function setFlag($name, $value)
	{
		$this->configure($name, $value);
	}

	function getFlag($name)
	{
		return $this->getConfiguration($name);
	}

	function setRequestURI($sRequestURI)
	{
		$this->configure('requestURI', $sRequestURI);
	}

	function getRequestURI()
	{
		return $this->getConfiguration('requestURI');
	}

	function setDefaultMode($sDefaultMode)
	{
		$this->configure('defaultMode', $sDefaultMode);
	}

	function getDefaultMode()
	{
		return $this->getConfiguration('defaultMode');
	}

	function setDefaultMethod($sMethod)
	{
		$this->configure('defaultMethod', $sMethod);
	}

	function getDefaultMethod()
	{
		return $this->getConfiguration('defaultMethod');
	}

	function setWrapperPrefix($sPrefix)
	{
		$this->configure('wrapperPrefix', $sPrefix);
	}

	function getWrapperPrefix()
	{
		return $this->getConfiguration('wrapperPrefix');
	}

	function setLogFile($sFilename)
	{
		$this->configure('logFile', $sFilename);
	}

	function getLogFile()
	{
		return $this->getConfiguration('logFile');
	}

	function registerFunction($mFunction, $sIncludeFile=null)
	{
		$xuf =& new xajaxUserFunction($mFunction, $sIncludeFile);
		return $this->register(XAJAX_FUNCTION, $xuf);
	}

	function registerCallableObject(&$oObject)
	{
		$mResult = FALSE;
		
		if(0 > version_compare(PHP_VERSION, '5.0')) eval('$mResult = $this->register(XAJAX_CALLABLE_OBJECT, &$oObject);');
		else $mResult = $this->register(XAJAX_CALLABLE_OBJECT, $oObject);
			
		return $mResult;
	}

	function registerEvent($sEventName, $mCallback)
	{
		$this->register(XAJAX_PROCESSING_EVENT, $sEventName, $mCallback);
	}
}

function xajaxErrorHandler($errno, $errstr, $errfile, $errline)
{
	$errorReporting = error_reporting();
	
	if(($errno & $errorReporting) == 0) return;
	if($errno == E_NOTICE) $errTypeStr = 'NOTICE';
	else if($errno == E_WARNING) $errTypeStr = 'WARNING';
	else if($errno == E_USER_NOTICE) $errTypeStr = 'USER NOTICE';
	else if($errno == E_USER_WARNING) $errTypeStr = 'USER WARNING';
	else if($errno == E_USER_ERROR) $errTypeStr = 'USER FATAL ERROR';
	else if(defined('E_STRICT') && $errno == E_STRICT) return;
	else $errTypeStr = 'UNKNOWN: '.$errno;
	
	$sCrLf = "\n";
	
	ob_start();
	echo $GLOBALS['xajaxErrorHandlerText'];
	echo $sCrLf;
	echo '----';
	echo $sCrLf;
	echo '[';
	echo $errTypeStr;
	echo '] ';
	echo $errstr;
	echo $sCrLf;
	echo 'Error on line ';
	echo $errline;
	echo ' of file ';
	echo $errfile;
	$GLOBALS['xajaxErrorHandlerText'] = ob_get_clean();
}