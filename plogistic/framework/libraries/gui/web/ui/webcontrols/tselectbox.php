<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TSelectBox renders to html element <select><option>...</select>
     *
     *     <?php
     *         $page->sbCountry->render(Array(
     *                                    "Style" => "width:200px;",
     *                                    "Size" => 25,
     *                                    "Multiple" => TRUE,
     *                                    "SubmitMode" => "ajax"
     *                                  )); ?>
     *
     * <u>Settings through TWebControl (Optional):</u>
     *
     *     setProperty("Class", "cssSomething"); // CSS class name
     *     setProperty("Style", "font-family:Arial;font-size:12px"); // CSS style
     *     setProperty("Size", 10); // How many items to show
     *     setProperty("Multiple", TRUE); // For allowing multiple selection
     *     setProperty("InitialValue", "paris");
     *
     *     setProperty("SubmitMode", "js"); js|post|ajax
     *
     * <u>Example:</u>
     *     // loads array
     *     $arr = Array(
     *               "ln" => "London",
     *               "pa" => "Paris",
     *               "mu" => "Munich"
     *             );
     *     
     *     $this->selNames->loadFromArray($arr);
     *     // or
     *     $this->selNames->loadFromArray($arr, 2);
     *     
     *     
     *     // loads data source
     *     $sql = TSqlStatement::sql("default.sql", "SITES_SQL");
     *     $sql->bindBool(":param1", true);
     *     $sql->bindInt(":param2", 3);
     *     $dataSource = db()->select($sql);
     *     
     *     $this->selNames->loadFromDataSource($dataSource, "site_id", "site_title");
     *     
     *     
     *     // loads tree
     *     $tree = new TTree();
     *     $tree->loadFromXmlFile(PROTECTED_XML_DIR . "tree.xml");
     *     $this->selNames->loadFromTree($tree, "text", "text");
     *     
     *     
     *     // add single element
     *     $this->selNames->addOption("mo", "Moscow");
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    class TSelectBox extends TWebControl {

        /**
         * For every value in this array the item will be selected in the control
         * @var array
         */
    	  protected $fSelectedItems = Array();




        /**
         * Constructor
         */
    	  public function TSelectBox() {
    	      parent::TWebControl();
    	  }




        /**
         * Add option
         * 
         * @param string $value Value
         * @param string $text Text
         */
        public function addOption($value, $text) {
        	  $options = $this->getOptions();
        	  $options[] = Array("value" => $value, "text" => $text);
        	  $this->setProperty("options", $options);
        	  $this->setProperty("OptionsChanged", TRUE);
    	  }


        /**
         * Get options
         * 
         * @return array options
         */
        public function getOptions() {
        	  $options = $this->getProperty("options");
        	  if ($options === NULL || !is_array($options)) {
        	      $options = Array();
        	  }
        	  return $options;
    	  }


        /**
         * Clear options
         */
        public function clearOptions() {
        	  $this->setProperty("options", Array());
        	  $this->setProperty("OptionsChanged", TRUE);
    	  }


        /**
         * Get text
         * 
         * @return string the text of the selected item
         */
        public function getText() {
            $options = $this->getOptions();
        	  if ($options && count($options) > 0) {
        	      $val = $this->getValue();
        	      foreach($options as $arr) {
        	          if ($val == $arr["value"]) {
                        return $arr["text"];
        	          }
        	      }
        	  }

            if ($this->fSelectedItems && count($this->fSelectedItems) > 0) {
            	  foreach($this->fSelectedItems as $k => $v) {
            	      return $v;
            	  }
            }

            return NULL;
        }


        /**
         * Select item
         * 
         * @param string $value Value
         * @param bool $aValueChanged Mark value as changed
         */
        public function selectItem($value, $aValueChanged = TRUE) {
        	  $this->fSelectedItems[$value] = "";
            if ($aValueChanged === TRUE) {
                $this->setProperty("ValueChanged", TRUE);
            }
    	  }


        /**
         * Get selected items
         * 
         * @return array selected items
         */
        public function selectedItems() {
        	  return $this->fSelectedItems;
        }


        /**
         * Checks to see if particular item/option is selected
         *
         * @param string $value Item/option
         * @return bool True if $value is selected
         */
        public function isSelected($value) {
            return isset($this->fSelectedItems[$value]);
    	  }


        /**
         * Clear selected items
         */
        public function clearSelectedItems() {
        	  $this->fSelectedItems = Array();
        	  $this->setProperty("ValueChanged", TRUE);
        }


        /**
         * Set value
         *
         * @param mixed $aValue value
         * @param bool $aValueChanged Mark value as changed
         */
        public function setValue($aValue, $aValueChanged = TRUE) {
            $this->selectItem($aValue, $aValueChanged);
        }


        /**
         * Get value
         *
         * @return mixed Value
         */
        public function getValue() {
            if ($this->fSelectedItems && count($this->fSelectedItems) > 0) {
            	  foreach($this->fSelectedItems as $k => $v) {
            	      return $k;
            	  }
            }

            return NULL;
        }


        /**
         * Load from array
         * 
         * @param array $arr Values
         * @param integer $mode Optional. Default value is 0. Possible values - 0|1|2.
         *                       0 - Loads array keys as values and array values as texts.
         *                       1 - Loads array keys as values and texts.
         *                       2 - Loads array values as values and texts.
         */
        public function loadFromArray($arr, $mode = 0) {
        	  $this->clearOptions();
        	  foreach($arr as $key=>$val) {
        	      if ($mode === 0) {
        	          $this->addOption($key, $val);
        	      }
        	  	  else if ($mode === 1) {
        	  	      $this->addOption($key, $key);
        	  	  }
        	  	  else if ($mode === 2) {
        	  	      $this->addOption($val, $val);
        	  	  }
        	  }
        }


        /**
         * Load from data source
         * 
         * @param TDataSource $dataSource Data source
         * @param string $valueColumn Value column name
         * @param string $textColumn Text column name
         */
        public function loadFromDataSource($dataSource, $valueColumn, $textColumn) {
        	  $this->clearOptions();
    	      for ($i=0; $i < $dataSource->count(); $i++) {
                $dsrow = $dataSource->record($i);
                $this->addOption($dsrow[$valueColumn], $dsrow[$textColumn]);
            }
        }


        /**
         * Loads from tree
         * 
         * @param TTree $tree Tree
         * @param string $valueAttribute Value attribute
         * @param string $textAttribute Text attribute
         * @param bool $includeRoot Include root element. Optional. The default value is False
         */
        public function loadFromTree($tree, $valueAttribute, $textAttribute, $includeRoot = FALSE) {
        	  $this->clearOptions();
        	  $level = 0;

				    if ($includeRoot) {
				    	  $this->addOption($tree->attr[$valueAttribute], $tree->attr[$textAttribute]);
				    	  $level++;
				    }

            foreach ($tree->subNodes as $nodeObj) {
                $this->visitNode($nodeObj, $valueAttribute, $textAttribute, $level);
            }
        }


        /**
         * For internal use
         *
         * @param TTreeNode $nodeObj Tree node
         * @param string $valueAttribute Value attribute
         * @param string $textAttribute Text attribute
         * @param integer $level Current node level
         */
        protected function visitNode($nodeObj, $valueAttribute, $textAttribute, $level) {
        	  $this->addOption($nodeObj->attr[$valueAttribute], str_repeat(".", 6*($level)) . $nodeObj->attr[$textAttribute]);

				    if (count($nodeObj->subNodes)) {
				        foreach ($nodeObj->subNodes as $nodeObj2) {
                    $this->visitNode($nodeObj2, $valueAttribute, $textAttribute, $level+1);
                }
				    }
        }


        /**
         * Gets the html code for this web control
         * 
         * @return string The html code for this web control
         */
    	  protected function toHtml() {
    	      $html = new TContent();

    	      $html->appendText("<select id='{$this->fName}'");

    	      if ($this->hasProperty("Multiple", TRUE)) {
    	          $html->appendText(" name='{$this->fName}[]'");
    	      }
    	      else {
    	          $html->appendText(" name='{$this->fName}'");
    	      }

    	      if ($this->hasProperty("Size")) {
    	          $html->appendText(" size='" . $this->getProperty("Size") . "'");
    	      }

    	      if ($this->hasProperty("Multiple", TRUE)) {
    	          $html->appendText(" multiple='multiple'");
    	      }

    	      if ($this->hasProperty("Class")) {
    	          $html->appendText(" class='" . $this->getProperty("Class") . "'");
    	      }

    	      if ($this->hasProperty("Style")) {
    	          $html->appendText(" style='" . $this->getProperty("Style") . "'");
    	      }

    	      $html->appendText(">\n");

    	      if ($this->hasProperty("InitialValue") && !$this->getValue()) {
    	          $this->setValue($this->getProperty("InitialValue"));
    	      }

    	      $options = $this->getOptions();
        	  foreach($options as $arr) {
        	  	  $selStr = ($this->isSelected($arr["value"]) ? " selected=\"selected\"" : "");
        	  	  $html->appendText("<option value=\"" . htmlspecialchars($arr["value"], ENT_QUOTES) . "\"{$selStr}>" . $arr["text"] . "</option>\n");
        	  }

    	      $html->appendText("</select>");

    	      return $html->toString();
    	  }


        /**
         * Post back data event
         */
    	  protected function OnPostBackData() {
    	      $this->fSelectedItems = Array();

    	  	  $tmp = request()->getParam($this->fName);

    	  	  if (!$tmp) {
    	  	      return;
    	  	  }

    	  	  $tmp = stdToArray(json_decode($tmp));

    	  	  if (is_array($tmp) && count($tmp) > 0) {
                while (list($i, $opt) = each($tmp)) {
                    if (isset($opt['value']) && isset($opt['text'])) {
                        $this->fSelectedItems[$opt['value']] = $opt['text'];
                        if (!AJAX_REQUEST) {
                            $this->setProperty("ValueChanged", TRUE);
                        }
                    }
                }
    	  	  }
    	  }


        /**
         * Event method triggered for every control to 
         * register itself with the agent
         */
    	  protected function OnFinalize() {
            agent()->registerWidget($this->getName(), "TSelectBox", Array(
    	          "mode" => $this->getProperty("SubmitMode", "none"),
    	          "initialValue"    => $this->getProperty("InitialValue")
    	      ));
    	  }


        /**
         * Event method triggered for every control to apply 
         * possible state changes
         */
    	  protected function OnPossibleStateChange() {
    	      if (AJAX_REQUEST && $this->hasProperty("OptionsChanged", TRUE)) {
    	          agent()->call("page.{$this->fName}.setOptions", $this->getOptions());
    	      }

    	      if (AJAX_REQUEST && $this->hasProperty("ValueChanged", TRUE)) {
    	          agent()->call("page.{$this->fName}.setValue", array_keys($this->fSelectedItems));
    	      }
    	  }
    }
?>