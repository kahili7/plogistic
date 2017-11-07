<?

class TFILESYSTEM
{

    public function TFILESYSTEM()
    {
        
    }

    static public function getFileContent($fileName)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $fileName = str_replace("/", "\\", $fileName);
        }
        return file_get_contents($fileName);
    }

    static public function getFileSize($fileName)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $fileName = str_replace("/", "\\", $fileName);
        }

        return filesize($fileName);
    }

    static public function getFileLines($fileName)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $fileName = str_replace("/", "\\", $fileName);
        }

        $lines = Array();
        $handle = fopen($fileName, "r");

        while (!feof($handle))
        {
            $buffer = fgets($handle, 8192);
            $lines[] = $buffer;
        }

        fclose($handle);
        return $lines;
    }

    static public function getFileAsArray($fileName)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $fileName = str_replace("/", "\\", $fileName);
        }

        $lines = TFILESYSTEM::getFileLines($fileName);
        $r = Array();

        for ($i = 0; $i < count($lines); $i++)
        {
            $lineStr = $lines[$i];
            $lineArr = explode("=", $lineStr);

            if (count($lineArr) >= 2)
            {
                $leftPart = $lineArr[0];
                $rightPart = "";

                for ($j = 1; $j < count($lineArr); $j++)
                    $rightPart .= $lineArr[$j];

                $r[$leftPart] = $rightPart;
            }
        }

        return $r;
    }

    static public function saveFileContent($fileName, $content)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $fileName = str_replace("/", "\\", $fileName);
        }

        $handle = fopen($fileName, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }

    static public function appendFileContent($fileName, $content)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $fileName = str_replace("/", "\\", $fileName);
        }

        $handle = fopen($fileName, 'a');
        fwrite($handle, $content);
        fclose($handle);
    }

    static public function isFile($filePath)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $filePath = str_replace("/", "\\", $filePath);
        }

        return is_file($filePath);
    }

    static public function searchFile($dir, $fileName)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $dir = str_replace("/", "\\", $dir);
        }

        $subdirs = Array();
        $handle = opendir($dir);

        while (($dirItem = readdir($handle)) !== false)
        {
            $fullPath = $dir . $dirItem;

            if (is_dir($fullPath) && $dirItem != "." && $dirItem != "..")
            {
                $subdirs[] = $fullPath . DIRECTORY_SEPARATOR;
            }
            else if (is_file($fullPath))
            {
                if ($dirItem == $fileName)
                {
                    closedir($handle);
                    return $fullPath;
                }
            }
        }

        closedir($handle);

        foreach ($subdirs as $key => $subdir)
        {
            $founded = TFILESYSTEM::searchFile($subdir, $fileName);

            if ($founded)
            {
                return $founded;
            }
        }

        return null;
    }

    static public function buildDataSourceFromDirectory($aDir, $aExtensions = null)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $aDir = str_replace("/", "\\", $aDir);
        }

        $dataSource = new TDATASOURCE();
        $handle = opendir($aDir);

        if (!$handle)
        {
            return null;
        }

        while (($file = readdir($handle)) !== FALSE)
        {
            $record = Array();
            $parts = pathinfo($aDir . $file);

            if ($parts["basename"] == "." || $parts["basename"] == "..")
            {
                continue;
            }

            if ($aExtensions == null || (isset($parts["extension"]) && in_array(strtolower($parts["extension"]), $aExtensions)))
            {
                $record["name"] = $parts["basename"];
                $record["fullname"] = $aDir . $parts["basename"];
                $record["type"] = filetype($aDir . $file);
                $record["atime"] = toTimestamp(fileatime($aDir . $file));
                $record["mtime"] = toTimestamp(filemtime($aDir . $file));
                $record["size"] = filesize($aDir . $file);
                $record["extension"] = (isset($parts["extension"]) ? $parts["extension"] : "");
                $dataSource->addRecord($record);
            }
        }

        closedir($handle);
        $tmp = $dataSource->getRecords();
        usort($tmp, "__Cmp_SortFileNames");
        $dataSource->setRecords($tmp);
        return $dataSource;
    }

    static public function unzip($fileName)
    {
        $zip = zip_open($fileName);

        if ($zip)
        {
            while ($zip_entry = zip_read($zip))
            {
                if (zip_entry_open($zip, $zip_entry, "r"))
                {
                    $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    $this->saveFileContent(dirname($fileName) . "/" . zip_entry_name($zip_entry), $buf);
                    zip_entry_close($zip_entry);
                }
            }

            zip_close($zip);
        }
        else
        {
            echo "Can not open file " . $fileName;
        }
    }

    static public function createFile($filePath)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $filePath = str_replace("/", "\\", $filePath);
        }

        $handle = fopen($filePath, 'w');
        fclose($handle);
    }

    static public function deleteFile($filePath)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $filePath = str_replace("/", "\\", $filePath);
        }

        if (is_file($filePath))
        {
            unlink($filePath);
        }
    }

    static public function moveFile($sourceFile, $targetFile)
    {
        if (DIRECTORY_SEPARATOR === "\\")
        {
            $sourceFile = str_replace("/", "\\", $sourceFile);
            $targetFile = str_replace("/", "\\", $targetFile);
        }

        if (!is_file($sourceFile))
        {
            throw new Exception("Source file doesn't exist: " . $sourceFile);
        }

        if (is_dir($targetFile))
        {
            $sourceShortFile = basename($sourceFile);
            rename($sourceFile, addSafeDirSlash($targetFile) . $sourceShortFile);
        }
        else
        {
            self::deleteFile($targetFile);
            rename($sourceFile, $targetFile);
        }
    }

    static public function createDir($dirPath)
    {
        $dirPath = str_replace("\\", "/", $dirPath);
        $temp = explode("/", $dirPath);
        $currDir = "";

        for ($i = 0; $i < count($temp); $i++)
        {
            $currDir .= $temp[$i] . DIRECTORY_SEPARATOR;

            if (DIRECTORY_SEPARATOR === "\\")
            {
                $currDir = str_replace("/", "\\", $currDir);
            }

            if (!is_dir($currDir))
            {
                mkdir($currDir);
            }
        }
    }

    static public function deleteDir($dirPath)
    {

        $dirPath = addSafeBackSlash($dirPath);

        if (DIRECTORY_SEPARATOR === "\\")
        {
            $dirPath = str_replace("/", "\\", $dirPath);
        }

        if (!is_dir($dirPath))
        {
            return;
        }

        $handle = opendir($dirPath);

        while (($entry = readdir($handle)) !== FALSE)
        {
            if ($entry != "." && $entry != "..")
            {
                if (is_dir($dirPath . $entry))
                {
                    self::deleteDir($dirPath . $entry);
                }
                else if (is_file($dirPath . $entry))
                {
                    @unlink($dirPath . $entry);
                }
            }
        }

        closedir($handle);
        @rmdir($dirPath);
    }

    static public function copyDir($srcPath, $destPath)
    {
        $srcPath = addSafeBackSlash($srcPath);
        $destPath = addSafeBackSlash($destPath);

        if (DIRECTORY_SEPARATOR === "\\")
        {
            $srcPath = str_replace("/", "\\", $srcPath);
            $destPath = str_replace("/", "\\", $destPath);
        }

        if (!is_dir($srcPath))
        {
            return;
        }

        @mkdir($destPath, 0777);
        $handle = opendir($srcPath);

        while (($entry = readdir($handle)) !== FALSE)
        {
            if ($entry != "." && $entry != "..")
            {
                if (is_dir($srcPath . $entry))
                {
                    self::copyDir($srcPath . $entry, $destPath . $entry);
                }
                else if (is_file($srcPath . $entry))
                {
                    copy($srcPath . $entry, $destPath . $entry);
                }
            }
        }

        closedir($handle);
    }

}

function __Cmp_SortFileNames($o1, $o2)
{

    if ($o1["type"] == "dir" && $o2["type"] == "file")
    {
        return -1;
    }
    else if ($o1["type"] == "file" && $o2["type"] == "dir")
    {
        return 1;
    }
    else
    {
        return strcmp($o1["name"], $o2["name"]);
    }
}

?>