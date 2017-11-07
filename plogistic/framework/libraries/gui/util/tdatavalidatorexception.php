<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TDataValidatorException is thrown if error has been produced for every
     * checkXXX method of the class TDataValidator
     *
     * <u>Example:</u>
     *
     *     // data validator
     *     $validator = new TDataValidator();
     *     
     *     $validator->checkNotEmpty("", "Value must be specified."); // here the exception is raised
     * </pre>
     *
     * @package System.Util
     */
    class TDataValidatorException extends Exception {

    }

?>