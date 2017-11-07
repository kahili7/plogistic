<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TMenu is an abstract class, which all menu controls must inherit.
     * 
     * Subclasses: TFolderMenu, TSimpleMenu, TStripeMenu, TNavMenu, TVerticalMenu
     *
     * </pre>
     *
     * @package System.Web.UI.WebControls
     */
    abstract class TMenu extends TWebControl {

        /**
         * Every node in the menu has "roles" attribute as well.
         * When it is rendered, it is checked against these roles.
         * @var array
         */
    	  private $fRoles = Array();

        /**
         * Menu items
         * @var TTree
         */
    	  private $fMenuItems;

        /**
         * Menu items updated flag
         * @var bool
         */
    	  private $fMenuItemsUpdated;




        /**
         * Constructor
         */
    	  public function TMenu() {
    	  	  parent::TWebControl();
    	  	  $this->fMenuItems = new TTree();
    	  	  $this->fMenuItems->setNodePrefix($this->fName . "Node");
    	  }




        /**
         * Adds menu item to the list of items.
         *
         * @param string $id Item id
         * @param array $attr Attributes
         * @param string $parent Parent id
         */
        public function addItem($id, $attr, $parent = NULL) {

        	  if (!$parent || $parent == "") {
                $this->fMenuItems->id = $id;
                $this->fMenuItems->attr = $attr;
                return;
        	  }

            $p = $this->fMenuItems->findNode($parent);

            if ($p) {
            	  $node = new TTreeNode();
            	  $node->id = $id;
            	  $node->attr = $attr;
            	  $p->addNode($node);
            }

            $this->setProperty("ItemsUpdated", TRUE);
        }


        /**
         * Loads all menu items from a xml file.
         *
         * @param string $fileName File name
         */
        public function loadFromXmlFile($fileName) {
        	  $this->fMenuItems = new TTree();
        	  $this->fMenuItems->setNodePrefix($this->fName . "Node");
            $this->fMenuItems->loadFromXmlFile($fileName);

            $this->setProperty("ItemsUpdated", TRUE);
        }


        /**
         * Set menu tree
         *
         * @param TTree $tree Menu tree
         */
        public function setTree($tree) {
        	  $this->fMenuItems = $tree;
        	  $this->fMenuItems->setNodePrefix($this->fName . "Node");
            
            $this->setProperty("ItemsUpdated", TRUE);
        }


        /**
         * Clear menu items
         */
        public function clearItems() {
        	  $this->fMenuItems = new TTree();
        	  $this->fMenuItems->setNodePrefix($this->fName . "Node");
            
            $this->setProperty("ItemsUpdated", TRUE);
        }


        /**
         * Sort menu based on specific attribute key.
         *
         * @param string $attrKey Attribute key to be used
         * @param string $dataType The data type of the column
         * @param integer $direction Possible values SORT_ASCENDING|SORT_DESCENDING
         */
        public function sortBy($attrKey, $dataType = "string", $direction = SORT_ASCENDING) {
        	  $this->fMenuItems->sortBy($attrKey, $dataType, $direction);
        }


        /**
         * Get items
         *
         * @param TTreeNode $treeNode Tree node
         * @return array items as array
         */
    	  protected function getItems($treeNode = NULL) {
    	      if ($treeNode === NULL) {
    	          $treeNode = $this->fMenuItems;
    	      }

    	  	  $list = Array();

            $rolesStr = (isset($treeNode->attr["roles"]) ? $treeNode->attr["roles"] : "");
            $rolesArr = explode(",", $rolesStr);

            if (trim($rolesStr) == "" || trim($rolesStr) == "*" || count(array_intersect($rolesArr, $this->fRoles)) > 0) {
            	  unset($treeNode->attr["roles"]);
            	  $list[] = Array(
            	      "id" => $treeNode->id,
            	      "a" => array_change_key_case($treeNode->attr, CASE_LOWER),
            	      "p" => $treeNode->parent
            	  );

                foreach ($treeNode->subNodes as $subNodeId => $subNode) {
                    $list = array_merge($list, $this->getItems($subNode));
                }
            }

    	  	  return $list;
        }


        /**
         * Set roles
         *
         * @param array $aRoles Roles
         */
        public function setRoles($aRoles) {
        	  $this->fRoles = $aRoles;
        }


        /**
         * Load state event
         */
    	  protected function OnLoadState() {
    	  	  parent::OnLoadState();

    	      if ($this->fKeepState) {
    	      	  if (isset($this->codeProps["__Roles"])) {
    	      	      $this->fRoles = $this->codeProps["__Roles"];
    	      	  }
    	      	  if (isset($this->codeProps["__menuItems"])) {
    	      	      $this->fMenuItems = $this->codeProps["__menuItems"];
    	      	  }
    	      }
    	  }


        /**
         * Save state event
         */
    	  protected function OnSaveState() {
    	      if ($this->fKeepState) {
    	          $this->codeProps["__Roles"] = $this->fRoles;
    	          $this->codeProps["__menuItems"] = $this->fMenuItems;
    	      }

    	      parent::OnSaveState();
    	  }


        /**
         * Event method triggered for every control to 
         * register itself with the agent
         */
    	  protected function OnFinalize() {
            //
    	  }


        /**
         * Event method triggered for every control to apply 
         * possible state changes
         */
    	  protected function OnPossibleStateChange() {
    	      if (AJAX_REQUEST && $this->hasProperty("ItemsUpdated", TRUE)) {
    	          agent()->call("page.{$this->fName}.setItems", $this->getItems());
    	          agent()->call("page.{$this->fName}.apply");
    	      }
    	  }
    }
?>