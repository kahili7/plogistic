<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TLabel renders to DIV or SPAN html element.
     *
     * <u>Settings through TWebControl (Optional):</u>
     *
     
     *     setProperty("Text", "I am label"); // label
     *     setProperty("DisplayWhenEmpty", FALSE); // Whether to visualize the DIV or SPAN
     *     setProperty("Class", "cssSomething"); // CSS class name
     *     setProperty("Style", "font-family:Arial;font-size:12px"); // CSS style
     *     setProperty("TagName", "div"); // div|span
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    class TLabel extends TWebControl {

        /**
         * Constructor
         */
    	  public function TLabel() {
    	  	  parent::TWebControl();
    	  }




        /**
         * Sets the text
         *
         * @param string $value Text
         */
        public function setText($value) {
            $this->setProperty("Text", $value);
            $this->setValue($value);
        }


        /**
         * Gets the text
         *
         * @return string Text
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

    	  	  $tagName = "span";
    	      if ($this->hasProperty("TagName", "div", TRUE)) {
    	          $tagName = "div";
    	      }

    	      $html = new TContent();

    	      $html->appendText("<{$tagName} id='{$this->fName}' ");

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

    	      $html->appendText("</{$tagName}>");

    	      return $html->toString();
    	  }


        /**
         * Event method triggered for every control to 
         * register itself with the agent
         */
    	  protected function OnFinalize() {
            agent()->registerWidget($this->getName(), "TLabel");
    	  }


        /**
         * Event method triggered for every control to apply 
         * possible state changes
         */
    	  protected function OnPossibleStateChange() {
    	      if ($this->hasProperty("DisplayWhenEmpty", FALSE)) {
    	          agent()->call("page.{$this->fName}.setDisplayWhenEmpty", FALSE);
    	          if (!$this->hasProperty("Text")) {
    	              agent()->call("page.{$this->fName}.setDisplay", FALSE);
    	          }
    	      }

    	      if (AJAX_REQUEST && $this->hasProperty("Text")) {
    	          agent()->call("page.{$this->fName}.setValue", $this->getText());
    	      }
    	  }
    	
    }
    
?>