<?

class TFLASHRESOURCEPACKAGE
{

    private $fStream;
    private $fResources;
    private $fSecretKey;

    public function TFLASHRESOURCEPACKAGE($aResources, $aSecretKey = NULL)
    {
        $this->fResources = $aResources;
        $this->fSecretKey = $aSecretKey;
        $this->fStream = "";
    }

    public function binary()
    {
        $this->writeInt(count($this->fResources));

        while (list($key, $filePath) = each($this->fResources))
        {
            $this->writeByteArray($key);

            if (preg_match("/^.*(jpg|jpeg|gif|png)$/i", $filePath))
            {
                $this->writeByte(1);
            }
            else if (preg_match("/^.*(txt|html|xml|properties)$/i", $filePath))
            {
                $this->writeByte(2);
            }
            else if (preg_match("/^.*(swf)$/i", $filePath))
            {
                $this->writeByte(3);
            }
            else
            {
                exit("Wrong file extension. The only valid file extensions are:jpg, jpeg, gif, png, swf, txt, html, xml, properties");
            }

            $content = file_get_contents($filePath);
            $this->writeByteArray($content);
        }

        if (isset($this->fSecretKey) && $this->fSecretKey != "")
        {
            $this->fStream = TSECURITYFACTORY::createInstance("XXTEA", $this->fSecretKey)->encrypt($this->fStream);
        }

        return $this->fStream;
    }

    private function intToBinary($aValue, $aBytesCount)
    {
        $output = "";
        $temp = $aValue;

        for ($i = 0; $i < $aBytesCount * 8; $i++)
        {
            $output = ($temp & 1) . $output;   // store the binary one's place digit
            $temp = $temp >> 1;                // shift temp right one binary digit
        }

        return $output;
    }

    private function binaryToInt($aBinaryString)
    {
        return bindec($aBinaryString);
    }

    private function writeInt($aValue)
    {
        $bin = $this->intToBinary($aValue, 4);
        $this->writeByte($this->binaryToInt(substr($bin, 0, 8)));
        $this->writeByte($this->binaryToInt(substr($bin, 8, 8)));
        $this->writeByte($this->binaryToInt(substr($bin, 16, 8)));
        $this->writeByte($this->binaryToInt(substr($bin, 24, 8)));
    }

    private function writeBytes($aValue)
    {
        for ($i = 0; $i < strlen($aValue); $i++)
        {
            $this->writeByte(ord($aValue[$i]));
        }
    }

    private function writeByteArray($aValue)
    {
        $this->writeInt(strlen($aValue));
        $this->writeBytes($aValue);
    }

    private function writeByte($aByte)
    {
        $this->fStream .= chr($aByte);
    }

}

?>