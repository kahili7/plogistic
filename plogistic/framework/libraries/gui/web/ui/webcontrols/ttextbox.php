<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TTextBox corresponds to html controls TEXTAREA or INPUT 
     * depending on the MultiLine property.
     *
     *     <?php $page->txtUsername->render(Array(
     *                                          "Style" => "width:200px"
     *                                         )); ?>
     *
     * <u>Settings through TWebControl (Optional):</u>
     *
     *     setProperty("Class", "cssSomething"); // CSS class name
     *     setProperty("Style", "font-family:Arial;font-size:12px"); // CSS style
     *     setProperty("InitialValue", "Paris");
     *
     *     // By setting this property the system will simulate button click
     *     setProperty("OnEnterClickButton", "btnLogin");
     *
     *     setProperty("Multiline", TRUE); // TRUE or FALSE
     *     setProperty("Readonly", TRUE); // TRUE or FALSE
     *     setProperty("PasswordMode", TRUE); // TRUE or FALSE
     *
     *     // Only when 'Multiline' is TRUE
     *     setProperty("Wrap", "off"); // 'soft', 'hard' or 'off'
     *     setProperty("Cols", 25);
     *     setProperty("Rows", 10);
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    class TTextBox extends TWebControl {

        /**
         * Constructor
         */
    	  public function TTextBox() {
    	  	  parent::TWebControl();
        }




        /**
         * Add line
         * 
         * @param string $aText Text
         */
    	  public function addLine($aText) {
            $value = $this->getValue();
            if (!$value) {
                $value = "";
            }
            $value .= $aText . "\n";
    	      $this->setValue($value);
    	  }


        /**
         * Gets the html code for this web control
         * 
         * @return string The html code for this web control
         */
    	  protected function toHtml() {
    	      return isTrue($this->getProperty("MultiLine")) ? $this->multiLine() : $this->singleLine();
    	  }


        /**
         * For internal use
         *
         * @return string Html
         */
    	  private function singleLine() {
    	      $mode = isTrue($this->getProperty("PasswordMode")) ? "password" : "text";

    	      $html = new TContent();

    	      $html->appendText("<input type='{$mode}' id='{$this->fName}' name='{$this->fName}'");

    	      if ($this->getValue() !== NULL) {
    	          $html->appendText(" value='" . htmlspecialchars($this->getValue(), ENT_QUOTES) . "'");
    	      }
    	      else if ($this->hasProperty("InitialValue")) {
    	          $html->appendText(" value='" . htmlspecialchars($this->getProperty("InitialValue"), ENT_QUOTES) . "'");
    	      }

    	      if ($this->hasProperty("Class")) {
    	          $html->appendText(" class='" . $this->getProperty("Class") . "'");
    	      }

    	      if ($this->hasProperty("Style")) {
    	          $html->appendText(" style='" . $this->getProperty("Style") . "'");
    	      }

    	      if ($this->hasProperty("Readonly", TRUE)) {
    	          $html->appendText(" readonly");
    	      }

    	      $html->appendText(" />");

    	      return $html->toString();
    	  }


        /**
         * For internal use
         *
         * @return string Html
         */
    	  private function multiLine() {

    	      $html = new TContent();

    	      $html->appendText("<textarea id='{$this->fName}' name='{$this->fName}'");

    	      if ($this->hasProperty("Class")) {
    	          $html->appendText(" class='" . $this->getProperty("Class") . "'");
    	      }

    	      if ($this->hasProperty("Style")) {
    	          $html->appendText(" style='" . $this->getProperty("Style") . "'");
    	      }

    	      if ($this->hasProperty("wrap")) {
    	          $html->appendText(" wrap='" . $this->getProperty("wrap") . "'");
    	      }

    	      if ($this->hasProperty("Readonly", TRUE)) {
    	          $html->appendText(" readonly");
    	      }

    	      if ($this->hasProperty("Cols")) {
    	          $html->appendText(" cols='" . $this->getProperty("Cols") . "'");
    	      }

    	      if ($this->hasProperty("Rows")) {
    	          $html->appendText(" rows='" . $this->getProperty("Rows") . "'");
    	      }

    	      $html->appendText(">");

    	      if ($this->getValue() !== NULL) {
    	          $html->appendText($this->getValue());
    	      }
    	      else if ($this->hasProperty("InitialValue")) {
    	          $html->appendText($this->getProperty("InitialValue"));
    	      }

    	      $html->appendText("</textarea>");

    	      return $html->toString();
    	  }


        /**
         * Post back data event
         */
    	  protected function OnPostBackData() {
    	  	  $this->setValue(request()->getParam($this->getName()), FALSE);
    	  }


        /**
         * Event method triggered for every control to 
         * register itself with the agent
         */
    	  protected function OnFinalize() {
            agent()->registerWidget($this->getName(), "TTextBox", Array(
    	          "OnEnterClickButton" => $this->getProperty("OnEnterClickButton")
    	      ));
    	  }


        /**
         * Event method triggered for every control to apply 
         * possible state changes
         */
    	  protected function OnPossibleStateChange() {
    	      if (AJAX_REQUEST && $this->isValueChanged()) {
    	          agent()->call("page.{$this->fName}.setValue", $this->getValue());
    	      }
    	  }


    }
?>