<?php

    /**
     * <pre>
     *
     * TSwf renders to html element <embed> or <object> depending on the browser.
     * It's purpose is to display flash content
     *
     * <u>Settings through TWebControl (Optional):</u>
     *
     *     setProperty("Src", "path_to_file");
     *     setProperty("Width", 500);
     *     setProperty("Height", 400);
     *     setProperty("scale", "path_to_file");
     *     setProperty("wmode", "path_to_file");
     *     setProperty("quality", 10);
     *     setProperty("align", "center");
     *     setProperty("bgcolor", "#FFFFFF");
     *     setProperty("allowScriptAccess", "");
     *     setProperty("allowFullScreen", "");
     *     setProperty("FlashVars", "");
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    class TSwf extends TWebControl {

        /**
         * Constructor
         */
    	  public function TSwf() {
    	      parent::TWebControl();
    	  }




        /**
         * Gets the html code for this web control
         * 
         * @return string The html code for this web control
         */
    	  protected function toHtml() {
            $page = TPage::getInstance();

            /**
             * Call appropriate method according to type of browser - IE, Firefox, Opera, etc.
             */
    	  	  if ($page->agent->isFirefox() || $page->agent->isMozilla())
    	  	      return $this->toHtml_embed();

    	  	  else if ($page->agent->isOpera())
    	  	      return $this->toHtml_object();

    	  	  else if ($page->agent->isSafari())
    	  	      return $this->toHtml_object();

    	  	  else if ($page->agent->isIE())
    	  	      return $this->toHtml_object();

    	  	  else if ($page->agent->isChrome())
    	  	      return $this->toHtml_object();

    	  	  else
    	  	      return $this->toHtml_object();
    	  }



        /**
         * Gets the html code for this web control - for Internet explorer
         * 
         * @return string The html code for this web control
         */
    	  private function toHtml_object() {

            $s = "        ";
    	      $html = "";
    	      $html .= "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'\n";
    	      $html .= $s . "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab'\n";

    	      // add id attribute
    	      $html .= $s . "id='" . $this->fName . "' name='" . $this->fName . "'\n";

    	      // add width attribute
    	      if ($this->getProperty("width"))
    	          $html .= $s . "width='" . $this->getProperty("width") . "'\n";

    	      // add height attribute
    	      if ($this->getProperty("height"))
    	          $html .= $s . "height='" . $this->getProperty("height") . "'\n";

    	      // add align attribute
    	      if ($this->getProperty("align"))
    	          $html .= $s . "align='" . $this->getProperty("align") . "'\n";

    	      $html = rtrim($html, "\n");
    	      $html .= ">\n";

    	      // add src attribute
    	      if ($this->getProperty("src"))
    	          $html .= $s . "<param name='movie' value='" . $this->getProperty("src") . "' />\n";

    	      // add scale attribute
    	      if ($this->getProperty("scale"))
    	          $html .= $s . "<param name='scale' value='" . $this->getProperty("scale") . "' />\n";

    	      // add bgcolor attribute
    	      if ($this->getProperty("bgcolor"))
    	          $html .= $s . "<param name='bgcolor' value='" . $this->getProperty("bgcolor") . "' />\n";

    	      // add wmode attribute
    	      if ($this->getProperty("wmode"))
    	          $html .= $s . "<param name='wmode' value='" . $this->getProperty("wmode") . "' />\n";

    	      // add allowScriptAccess attribute
    	      if ($this->getProperty("allowScriptAccess"))
    	          $html .= $s . "<param name='allowScriptAccess' value='" . $this->getProperty("allowScriptAccess") . "' />\n";

    	      // add allowFullScreen attribute
    	      if ($this->getProperty("allowFullScreen"))
    	          $html .= $s . "<param name='allowFullScreen' value='" . $this->getProperty("allowFullScreen") . "' />\n";

    	      // add FlashVars attribute
    	      if ($this->getProperty("FlashVars"))
    	          $html .= $s . "<param name='FlashVars' value='" . $this->getProperty("FlashVars") . "' />\n";

    	      // add quality attribute
    	      if ($this->getProperty("quality"))
    	          $html .= $s . "<param name='quality' value='" . $this->getProperty("quality") . "' />\n";

    	      $html .= "</object>";

    	      return $html;
    	  }


        /**
         * Gets the html code for this web control - for Firefox, Mozilla, ...
         * 
         * @return string The html code for this web control
         */
    	  private function toHtml_embed() {
            $s = "       ";
    	      $html = "";
    	      $html .= "<embed id='" . $this->fName . "'\n";
    	      $html .= $s . "type='application/x-shockwave-flash'\n";
    	      $html .= $s . "pluginspage='http://www.macromedia.com/go/getflashplayer'\n";

    	      // add src attribute
    	      if ($this->getProperty("src"))
    	          $html .= $s . "src='" . $this->getProperty("src") . "'\n";

    	      // add width attribute
    	      if ($this->getProperty("width"))
    	          $html .= $s . "width='" . $this->getProperty("width") . "'\n";

    	      // add height attribute
    	      if ($this->getProperty("height"))
    	          $html .= $s . "height='" . $this->getProperty("height") . "'\n";

    	      // add scale attribute
    	      if ($this->getProperty("scale"))
    	          $html .= $s . "scale='" . $this->getProperty("scale") . "'\n";

    	      // add wmode attribute
    	      if ($this->getProperty("wmode"))
    	          $html .= $s . "wmode='" . $this->getProperty("wmode") . "'\n";

    	      // add quality attribute
    	      if ($this->getProperty("quality"))
    	          $html .= $s . "quality='" . $this->getProperty("quality") . "'\n";

    	      // add align attribute
    	      if ($this->getProperty("align"))
    	          $html .= $s . "align='" . $this->getProperty("align") . "'\n";

    	      // add bgcolor attribute
    	      if ($this->getProperty("bgcolor"))
    	          $html .= $s . "bgcolor='" . $this->getProperty("bgcolor") . "'\n";

    	      // add allowScriptAccess attribute
    	      if ($this->getProperty("allowScriptAccess"))
    	          $html .= $s . "allowScriptAccess='" . $this->getProperty("allowScriptAccess") . "'\n";

    	      // add allowFullScreen attribute
    	      if ($this->getProperty("allowFullScreen"))
    	          $html .= $s . "allowFullScreen='" . $this->getProperty("allowFullScreen") . "'\n";

    	      // add FlashVars attribute
    	      if ($this->getProperty("FlashVars"))
    	          $html .= $s . "FlashVars='" . $this->getProperty("FlashVars") . "'\n";

    	      $html = rtrim($html, "\n");
    	      $html .= ">\n";

    	      $html .= "</embed>";

    	      return $html;
    	  }
    }
?>