<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TImageButton renders to html element <a onclick=''><img src=''/></a>
     *
     *     <?php $page->btnImage1->render(Array(
     *                                       "Src" => "/assets/images/iamtimagebutton.jpg",
     *                                       "Alt" => "I am TImageButton",
     *                                       "Title" => "I am TImageButton")); ?>
     *
     * <u>Settings through TWebControl (Optional):</u>
     *
     *     setProperty("Src", "path_to_file");
     *     setProperty("Alt", "I am image"); // Alternate text - Internet explorer
     *     setProperty("Title", "I am image");// Alternate text - Mozilla
     *     setProperty("Class", "cssSomething"); // CSS class name
     *     setProperty("Style", "font-family:Arial;font-size:12px"); // CSS style
     *
     *     setProperty("SubmitMode", "js"); js|post|ajax|none
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    class TImageButton extends TAbstractButton {

        /**
         * Constructor
         */
    	  public function TImageButton() {
    	  	  parent::TAbstractButton();
    	  }




        /**
         * Gets the html code for this web control
         * 
         * @return string The html code for this web control
         */
    	  protected function toHtml() {
    	      $html = new TContent();
    	      $html->appendText("<a id='{$this->fName}' name='{$this->fName}' href='#' style='outline-style:none;'>");
    	      $html->appendText("<img id='{$this->fName}Image' name='{$this->fName}Image'");
    	      $html->appendText(" src='" . $this->getProperty("src") . "'", $this->hasProperty("src"));
    	      $html->appendText(" alt='" . $this->getProperty("alt") . "'", $this->hasProperty("alt"));
    	      $html->appendText(" title='" . $this->getProperty("title") . "'", $this->hasProperty("title"));
    	      $html->appendText(" style='border-style:none;' />");
    	      $html->appendText("</a>");

    	      return $html->toString();
    	  }


        /**
         * Event method triggered for every control to 
         * register itself with the agent
         */
    	  protected function OnFinalize() {
            agent()->registerWidget($this->getName(), "TImageButton", Array(
                  "mode" => $this->getProperty("SubmitMode", "post"),
                  "cm" => $this->getConfirmMessage()
    	      ));
    	  }
    }
?>