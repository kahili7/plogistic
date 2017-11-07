<?

class TCACHE
{

    static private $fsFileBased = FALSE;

    static public function setFileBased()
    {
        self::$fsFileBased = TRUE;
    }

    static public function setMemoryBased()
    {
        self::$fsFileBased = FALSE;
    }

    static public function isMemoryAvailable()
    {
        return function_exists('apc_store');
    }

    static public function setProperty($key, $value)
    {

        if (self::$fsFileBased)
        {
            if ($value === NULL)
            {
                $this->removeProperty($key);
                return;
            }

            $md5key = md5($key);
            $targetInfoFile = PROTECTED_PERSISTENT_DIR . "cache_state/" . $md5key . ".info";
            $targetContentFile = PROTECTED_PERSISTENT_DIR . "cache_state/" . $md5key . ".cache";
            file_put_contents($targetInfoFile, $key);
            file_put_contents($targetContentFile, serialize($value));
            return;
        }

        if (!TCACHE::isMemoryAvailable())
        {
            return;
        }

        TAPPLICATION::getInstance()->lock();

        $appName = TAPPLICATION::getInstance()->getName();
        $keys = apc_fetch($appName . '-keys-zW3jRd2-04FeC2Ji');

        if (!$keys)
            $keys = Array();

        $keys[$key] = time();

        apc_store($appName . '-keys-zW3jRd2-04FeC2Ji', $keys);
        apc_store($appName . '-' . $key, new ArrayObject(Array($value)));
        TAPPLICATION::getInstance()->unlock();
    }

    static public function getProperty($key)
    {

        if (self::$fsFileBased)
        {
            $md5key = md5($key);
            $targetInfoFile = PROTECTED_PERSISTENT_DIR . "cache_state/" . $md5key . ".info";
            $targetContentFile = PROTECTED_PERSISTENT_DIR . "cache_state/" . $md5key . ".cache";

            if (file_exists($targetInfoFile) && file_exists($targetContentFile))
            {
                $info = file_get_contents($targetInfoFile);
                $str = file_get_contents($targetContentFile);

                if ($key == $info && $str)
                {
                    return unserialize($str);
                }
            }

            return NULL;
        }

        if (!TCACHE::isMemoryAvailable())
        {
            return null;
        }

        $appName = TAPPLICATION::getInstance()->getName();
        $collection = apc_fetch($appName . '-' . $key);

        if (!$collection)
        {
            return NULL;
        }

        $tmpArray = $collection->getArrayCopy();

        if (isset($tmpArray[0]))
        {
            return $tmpArray[0];
        }

        return NULL;
    }

    static public function removeProperty($key)
    {
        if (self::$fsFileBased)
        {
            $md5key = md5($key);
            $targetInfoFile = PROTECTED_PERSISTENT_DIR . "cache_state/" . $md5key . ".info";
            $info = file_get_contents($targetInfoFile);

            if ($key == $info && file_exists($targetInfoFile))
            {
                TFILESYSTEM::deleteFile($targetInfoFile);
            }

            $targetContentFile = PROTECTED_PERSISTENT_DIR . "cache_state/" . $md5key . ".cache";

            if ($key == $info && file_exists($targetContentFile))
            {
                TFILESYSTEM::deleteFile($targetContentFile);
            }

            return;
        }

        if (!TCACHE::isMemoryAvailable())
        {
            return;
        }

        TAPPLICATION::getInstance()->lock();
        $appName = TAPPLICATION::getInstance()->getName();
        $keys = apc_fetch($appName . '-keys-zW3jRd2-04FeC2Ji');

        if (!$keys)
            $keys = Array();

        if (isset($keys[$key]))
            unset($keys[$key]);

        apc_store($appName . '-keys-zW3jRd2-04FeC2Ji', $keys);
        apc_delete($appName . '-' . $key);
        TAPPLICATION::getInstance()->unlock();
    }

    static public function getPropertyList()
    {
        if (self::$fsFileBased)
        {
            $tmp = Array();
            $targetDirectory = PROTECTED_PERSISTENT_DIR . "cache_state/";
            $handle = opendir($targetDirectory);

            while (($file = readdir($handle)) !== FALSE)
            {
                if (stripos($file, ".info") !== FALSE && is_file($targetDirectory . $file))
                {
                    $tmp[] = file_get_contents($targetDirectory . $file);
                }
            }

            closedir($handle);
            return $tmp;
        }

        if (!TCACHE::isMemoryAvailable())
        {
            return Array();
        }

        TAPPLICATION::getInstance()->lock();
        $appName = TAPPLICATION::getInstance()->getName();
        $keys = apc_fetch($appName . '-keys-zW3jRd2-04FeC2Ji');

        if (!$keys)
            $keys = Array();

        TAPPLICATION::getInstance()->unlock();
        return $keys;
    }

    static public function keys()
    {
        return self::getPropertyList();
    }

    static public function clear()
    {
        if (self::$fsFileBased)
        {
            $targetDirectory = PROTECTED_PERSISTENT_DIR . "cache_state/";
            $handle = opendir($targetDirectory);

            while (($file = readdir($handle)) !== FALSE)
            {
                TFILESYSTEM::deleteFile($targetDirectory . $file);
            }

            closedir($handle);
            return;
        }

        if (!TCACHE::isMemoryAvailable())
        {
            return;
        }

        TAPPLICATION::getInstance()->lock();
        $appName = TAPPLICATION::getInstance()->getName();
        $keys = apc_fetch($appName . '-keys-zW3jRd2-04FeC2Ji');

        if (!$keys)
            $keys = Array();

        foreach ($keys as $key => $value)
        {
            apc_delete($appName . '-' . $key);
        }

        apc_delete($appName . '-keys-zW3jRd2-04FeC2Ji');
        TAPPLICATION::getInstance()->unlock();
    }

}

?>