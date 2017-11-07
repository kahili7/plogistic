<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TLinkButton renders to html element <a>Click me</a>
     *
     * <u>Settings through TWebControl (Optional):</u>
     *
     *     The same as TButton
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    class TLinkButton extends TAbstractButton {

        /**
         * Constructor
         */
    	  public function TLinkButton() {
    	  	  parent::TAbstractButton();
    	  }




        /**
         * Sets the text
         *
         * @param string $value Text on the button
         */
        public function setText($value) {
            $this->setProperty("Text", $value);
        }


        /**
         * Gets the text
         *
         * @return string Text on the button
         */
        public function getText() {
            return $this->getProperty("Text");
        }


        /**
         * Gets the html code for this web control
         * 
         * @return string The html code for this web control
         */
    	  protected function toHtml() {

    	      $html = new TContent();

    	      $html->appendText("<a id='{$this->fName}' name='{$this->fName}' href='#'");

    	      if ($this->hasProperty("Class")) {
    	          $html->appendText(" class='" . $this->getProperty("Class") . "'");
    	      }

    	      if ($this->hasProperty("Style")) {
    	          $html->appendText(" style='" . $this->getProperty("Style") . "'");
    	      }

    	      $html->appendText(">");

    	      if ($this->hasProperty("Text")) {
    	          $html->appendText($this->getProperty("Text"));
    	      }

    	      $html->appendText("</a>");

    	      return $html->toString();
    	  }


        /**
         * Event method triggered for every control to 
         * register itself with the agent
         */
    	  protected function OnFinalize() {
            agent()->registerWidget($this->getName(), "TLinkButton", Array(
                  "mode" => $this->getProperty("SubmitMode", "post"),
                  "cm" => $this->getConfirmMessage()
    	      ));
    	  }


        /**
         * Event method triggered for every control to apply 
         * possible state changes
         */
    	  protected function OnPossibleStateChange() {
    	      if (AJAX_REQUEST && $this->hasProperty("Text")) {
    	          agent()->call("page.{$this->fName}.setValue", $this->getProperty("Text"));
    	      }
    	  }
    }
?>