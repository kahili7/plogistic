<?

class TAPPLICATION
{

    static private $myself;

    static public function getInstance()
    {
        return self::$myself;
    }

    static public function createInstance()
    {
        if (is_object(self::$myself) == TRUE)
        {
            exit("Only one istance of TApplication can be created");
            return;
        }

        self::$myself = new TAPPLICATION();
        return self::$myself;
    }

    private $fName = "";
    private $fContexts = Array();
    private $fXHTML = FALSE;
    private $fCheckForSqlInjection = FALSE;
    private $fDevMode = FALSE;
    private $fDatabaseReference;
    public $errorHandler;
    private $fLockFile;
    private $fFilePointer;
    private $fDefaultLocale = "en";

    private function TAPPLICATION()
    {
        $this->fLockFile = SYS_WORKING_DIR . "app.lock";
        $this->errorHandler = new TPHPERRORHANDLER();
    }

    public function setName($aName)
    {
        $this->fName = $aName;
    }

    public function getName()
    {
        return $this->fName;
    }

    public function addContext($context)
    {
        $this->fContexts[] = $context;
    }

    public function findContextByHost($host)
    {
        for ($i = 0; $i < count($this->fContexts); $i++)
        {
            if ($this->fContexts[$i]->getHost() == $host || $this->fContexts[$i]->isHostAlias($host))
            {
                return $this->fContexts[$i];
            }
        }

        return NULL;
    }

    public function setDatabase($aDatabaseReference, $aId = NULL)
    {
        if (!$aId)
        {
            $this->fDatabaseReference = $aDatabaseReference;
        }
        else
        {
            TREGISTRY::setProperty("dbref-" . $aId, $aDatabaseReference);
        }
    }

    public function getDatabase($aId = NULL)
    {
        if (!$aId)
        {
            return $this->fDatabaseReference;
        }
        else
        {
            return TREGISTRY::getProperty("dbref-" . $aId);
        }
    }

    public function setCheckForSqlInjection($aValue)
    {
        $this->fCheckForSqlInjection = $aValue;
    }

    public function isCheckForSqlInjection()
    {
        return $this->fCheckForSqlInjection === TRUE;
    }

    public function setDevMode($aValue)
    {
        $this->fDevMode = $aValue;
    }

    public function isDevMode()
    {
        return $this->fDevMode === TRUE;
    }

    public function setXHTML($aValue)
    {
        $this->fXHTML = $aValue;
    }

    public function isXHTML()
    {
        return $this->fXHTML === TRUE;
    }

    public function lock()
    {
        $this->fFilePointer = fopen($this->fLockFile, "r+");
        flock($this->fFilePointer, LOCK_EX);
    }

    public function unlock()
    {
        flock($this->fFilePointer, LOCK_UN);
        fclose($this->fFilePointer);
    }

    public function setProperty($key, $value)
    {
        if ($value === NULL)
        {
            $this->removeProperty($key);
            return;
        }

        $targetFile = PROTECTED_PERSISTENT_DIR . "app_state/" . base64_encode($key);

        if (strlen($targetFile) > 255)
            fireApplicationError("TAPPLICATION::setProperty() - The 'key' is too long | " . $key);

        file_put_contents($targetFile, serialize($value));
    }

    public function getProperty($key)
    {
        $targetFile = PROTECTED_PERSISTENT_DIR . "app_state/" . base64_encode($key);

        if (is_file($targetFile))
        {
            $str = file_get_contents($targetFile);

            if ($str)
            {
                return unserialize($str);
            }
        }

        return NULL;
    }

    public function getPropertyList()
    {
        $tmp = Array();
        $targetDirectory = PROTECTED_PERSISTENT_DIR . "app_state/";
        $handle = opendir($targetDirectory);

        while (($file = readdir($handle)) !== FALSE)
        {
            if (is_file($targetDirectory . $file))
            {
                $tmp[] = base64_decode($file);
            }
        }

        closedir($handle);
        return $tmp;
    }

    public function removeProperty($key)
    {
        $targetFile = PROTECTED_PERSISTENT_DIR . "app_state/" . base64_encode($key);
        TFILESYSTEM::deleteFile($targetFile);
    }

    public function setDefaultLocale($locale)
    {
        $this->fDefaultLocale = $locale;
    }

    public function getDefaultLocale()
    {
        return $this->fDefaultLocale;
    }

}

?>