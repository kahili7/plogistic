<?

abstract class TPAGE
{

    static private $myself = NULL;

    static public function create($aClass = NULL)
    {
        if ($aClass)
        {
            self::$myself = new $aClass();
            self::$myself->setup();
        }
        else
        {
            foreach (get_declared_classes () as $declaredClass)
            {
                if (is_subclass_of($declaredClass, "TPage"))
                {
                    $reflClass = new ReflectionClass($declaredClass);

                    if (!$reflClass->isAbstract())
                    {
                        self::$myself = new $declaredClass();
                        self::$myself->setup();
                    }
                }
            }
        }

        if (TAPPLICATION::getInstance()->isCheckForSqlInjection())
        {
            if (!self::$myself->request->isSqlInjectionSafe())
            {
                exit(i18n("SYSMSG_ERROR_SQL_INJECTION_DETECTED"));
            }
        }

        if (self::$myself->isXHTML() && self::$myself->agent->isXHTMLSupported())
        {
            header("Content-type: application/xhtml+xml");

            if (self::$myself->isFrameset())
            {
                echo XHTML_FRAMESET . "\n";
            }
            else
            {
                echo XHTML_PAGE . "\n";
            }
        }

        return self::$myself;
    }

    static public function getInstance()
    {
        return self::$myself;
    }

    private $postBackFlag = FALSE;
    private $ajaxRequestFlag = FALSE;
    private $resubmitFlag = FALSE;
    private $framesetFlag = FALSE;
    private $xhtmlFlag = FALSE;
    private $xhtmlSetByCode = FALSE;
    private $fDestroyWidgetList = Array();
    public $request = NULL;
    public $response = NULL;
    public $agent = NULL;
    public $model = NULL;
    public $controlState = NULL;
    public $session = NULL;
    private $fErrors = Array();
    private $fMessages = Array();
    public $user = NULL;
    public $head = NULL;
    private $fClass = NULL;
    private $fStyle = NULL;
    public $style = NULL;
    private $fLookAndFeel = NULL;
    private $fUploadAllowed = FALSE;
    public $applicationContext = NULL;

    public function TPAGE()
    {
        
    }

    public function showError($errorMsg)
    {
        $this->fErrors[] = $errorMsg;
    }

    public function addError($errorMsg)
    {
        $this->fErrors[] = $errorMsg;
    }

    public function appendErrors($aErrors)
    {
        while (list(, $err) = each($aErrors))
        {
            $this->fErrors[] = $err;
        }
    }

    public function hasErrors()
    {
        return (count($this->fErrors) > 0);
    }

    public function getErrors()
    {
        return $this->fErrors;
    }

    public function showMessage($aMessage)
    {
        $this->fMessages[] = $aMessage;
    }

    public function addMessage($aMessage)
    {
        $this->fMessages[] = $aMessage;
    }

    public function appendMessages($aMessages)
    {
        while (list(, $msg) = each($aMessages))
        {
            $this->fMessages[] = $msg;
        }
    }

    public function hasMessages()
    {
        return (count($this->fMessages) > 0);
    }

    public function getMessages()
    {
        return $this->fMessages;
    }

    public function showConfirm($aMessage, $aYesHandler, $aNoHandler = NULL)
    {
        $this->agent->call("page.showServerConfirm", $aMessage, $aYesHandler, $aNoHandler);
    }

    protected function setTitle($title)
    {
        $this->head->setProperty("Title", $title);
    }

    public function setClass($value)
    {
        $this->fClass = $value;
    }

    public function getClass()
    {
        return $this->fClass;
    }

    public function setStyle($value)
    {
        $this->fStyle = $value;
    }

    public function getStyle()
    {
        return $this->fStyle;
    }

    public function changeOpacityColor($value)
    {
        $this->agent->call("page.changeOpacityColor", $value);
    }

    public function getFirstHitURI()
    {
        return $this->session->getProperty("FIRST_HIT_URI");
    }

    public function setFrameset($value)
    {
        $this->framesetFlag = $value;
    }

    public function isFrameset()
    {
        return $this->framesetFlag;
    }

    public function setXHTML($value)
    {
        $this->xhtmlFlag = $value;
        $this->xhtmlSetByCode = TRUE;
    }

    public function isXHTML()
    {
        if ($this->xhtmlSetByCode)
        {
            return $this->xhtmlFlag;
        }
        else
        {
            return TAPPLICATION::getInstance()->isXHTML();
        }
    }

    public function enableUpload()
    {
        $this->fUploadAllowed = TRUE;
    }

    public function disableUpload()
    {
        $this->fUploadAllowed = FALSE;
    }

    public function isUploadAllowed()
    {
        return $this->fUploadAllowed;
    }

    public function redirect($url, $parameters = Array())
    {
        $this->redirectGet($url, $parameters);
    }

    public function redirectPost($url, $parameters = Array(), $automatic = TRUE)
    {
        if (AJAX_REQUEST)
        {
            return $this->agent->call("page.redirect", $url, count($parameters) > 0 ? $parameters : NULL, Array("method" => "post"));
        }

        $html = "Redirecting ...";

        if ($automatic)
        {
            $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">";
            $html .= "<body onload='document.frm.submit()'>";
            $html .= "<form name='frm' method='post' action='{$url}'>";

            foreach ($parameters as $name => $value)
                $html .= "<input type='hidden' name='{$name}' value=\"" . str_replace("\"", "&quot;", $value) . "\" />";

            $html .= "</form>";
            $html .= "</body>";
            $html .= "</html>";
        }
        else
        {
            $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">";
            $html .= "<body>";
            $html .= "<form name='frm' method='post' action='{$url}'>";

            foreach ($parameters as $name => $value)
                $html .= "<input type='hidden' name='{$name}' value=\"" . str_replace("\"", "&quot;", $value) . "\" />";

            $html .= "<input type='submit' name='btnSubmit' value=\"Go\" />";
            $html .= "</form>";
            $html .= "</body>";
            $html .= "</html>";
        }

        echo $html;
        exit;
    }

    public function redirectGet($url, $parameters = Array(), $flushAjaxAndExit = FALSE)
    {
        if (AJAX_REQUEST)
        {
            $this->agent->call("page.redirect", $url, count($parameters) > 0 ? $parameters : NULL);

            if ($flushAjaxAndExit === TRUE)
            {
                echo response()->flush();
                exit;
            }

            return;
        }

        $str = "";

        foreach ($parameters as $name => $value)
        {
            $str .= urlencode($name) . "=" . urlencode($value) . "&";
        }

        if ($str)
        {
            $str = substr($str, 0, strlen($str) - 1);
            $url = $url . "?" . $str;
        }

        Header("Location:$url");
        exit;
    }

    public function loadHTTPS()
    {
        $this->redirect(str_replace("http://", "https://", request()->getUrl()));
    }

    public function isPostBack()
    {
        return $this->postBackFlag;
    }

    public function isAjaxRequest()
    {
        return $this->ajaxRequestFlag;
    }

    public function isResubmit()
    {
        return $this->resubmitFlag;
    }

    public function OnPageCreate()
    {
        
    }

    public function OnPageInit()
    {
        
    }

    public function OnPageLoad()
    {
        
    }

    public function setup()
    {
        $this->session = new THTTPSESSION();
        $this->request = new THTTPREQUEST();
        $this->response = new THTTPRESPONSE();
        $this->agent = new THTTPAGENT();
        $this->model = new TDATAMODEL();
        $this->controlState = new TCONTROLSTATE();
        $this->user = new THTTPUSER();
        $this->head = new THEAD();
        $this->style = new TSTYLE();

        if ($this->request->getParameter("_submit_type") == "ajax")
        {
            $this->ajaxRequestFlag = TRUE;
            define("AJAX_REQUEST", TRUE);
            TAJAXRESPONSE::createInstance();
        }
        else
        {
            define("AJAX_REQUEST", FALSE);
        }

        $locale = $this->getLocale();

        if ($locale != NULL)
        {
            $this->loadLocale($locale);
        }
        else
        {
            $locale = TAPPLICATION::getInstance()->getDefaultLocale();

            if ($locale != NULL)
            {
                $this->loadLocale($locale);
            }
            else
            {
                $this->setLocale("en");
            }
        }

        $appCtx = TAPPLICATION::getInstance()->findContextByHost($this->request->getHost());

        if ($appCtx == NULL)
        {
            $appCtx = new TAPPLICATIONCONTEXT($this->request->getHost());
            TAPPLICATION::getInstance()->addContext($appCtx);
        }

        $this->applicationContext = $appCtx;
        $allowed_ip_addresses = $appCtx->getAllowedIpAddresses();

        if (isset($allowed_ip_addresses) && !in_array($this->request->getRemoteIp(), $allowed_ip_addresses))
        {
            exit(i18n("SYSMSG_ERROR_NO_RIGHTS_TO_VIEW_PAGE"));
        }

        $uri = $this->request->getPhpSelf();
        $authToken = $appCtx->isAuthPage($uri);

        if (((!$this->user->isLogged() && $authToken !== FALSE) || ($this->user->isLogged() && $authToken !== FALSE && $authToken !== $this->user->getAuthToken())) && !preg_match("/.*qphp\.js\.php.*/i", $uri))
        {
            $loginPage = $appCtx->getLoginPage($authToken);

            if ($loginPage)
            {
                $this->session->setProperty("FIRST_HIT_URI", $this->request->getUri());
                return $this->redirectGet($loginPage, Array(), AJAX_REQUEST ? TRUE : FALSE);
            }
            else
            {
                exit(i18n("SYSMSG_ERROR_PAGE_REQUIRES_AUTHORIZATION"));
            }
        }

        $queryString = $this->request->getQueryString();

        if ($queryString && preg_match("/^" . SYS_INTERNAL_REDIRECT_PREFIX . ".*$/i", $queryString))
        {
            $temp = decryptArray(substr($queryString, 5));

            if ($temp != NULL)
            {
                while (list($key, $value) = each($temp))
                {
                    $_GET[$key] = $value;
                }
            }
            else
            {
                exit(i18n("SYSMSG_ERROR_APP_FAILURE"));
            }
        }

        $this->appendDataModelFromRequest();

        if (!$this->isAjaxRequest())
        {
            if ($_SERVER["HTTP_USER_AGENT"] == "Shockwave Flash" && preg_match("/^.*multipart.*form-data.*$/i", $_SERVER["CONTENT_TYPE"]))
            {
                $this->handleFlashFileUpload();
                exit;
            }

            $this->OnPageCreate();
            $this->initializeWebControls(TRUE);

            if ($this->request->getParam("_submit_type") == "post")
            {
                $this->postBackFlag = TRUE;
                $this->appendDataModelFromRequest();
                $this->OnLoadState();
            }

            if ($this->isPostBack())
            {
                $this->OnPostBackData();
            }
            else
            {
                $dialogID = $this->request->getParam("____dialogID");

                if ($dialogID)
                {
                    $this->model->setProperty("____dialogID", $dialogID, TRUE, TRUE);
                }
            }

            try
            {
                $this->OnPageInit();
                $this->OnPageLoad();
            }
            catch (Exception $ex)
            {
                $this->OnPageException($ex);
            }

            $_submit_source = $this->request->getParameter("_submit_source");
            $_submit_handler = $this->request->getParam("_submit_handler");

            if ($_submit_source != NULL)
            {
                $object_vars = get_object_vars($this);
                $webControl = isset($object_vars[$_submit_handler]) ? $object_vars[$_submit_handler] : NULL;

                if ($webControl != NULL && is_class($webControl, "TWEBCONTROL"))
                {
                    $this->$_submit_source->OnPostBackEvent();
                }
            }

            if ($_submit_handler != NULL)
            {
                if (method_exists($this, $_submit_handler) === TRUE)
                {
                    try
                    {
                        call_user_func_array(Array($this, $_submit_handler), Array());
                    }
                    catch (Exception $ex)
                    {
                        $this->OnPageException($ex);
                    }
                }
                else
                {
                    exit("Event method is not defined - " . $_submit_handler);
                }
            }

            $this->OnSaveState();
        }
        else
        {
            $this->initializeWebControls(FALSE);

            if ($this->request->getParam("___data_editor_cmd") == "____create_widgets")
            {
                $ajaxResponse = TAJAXRESPONSE::getInstance();
                $ajaxResponse->addParameter("_data_trackID", $this->request->getParam("_data_trackID"));
                $this->buildAjaxControls($ajaxResponse);
                echo $ajaxResponse->flush();
                exit;
            }

            $ajaxResponse = TAJAXRESPONSE::getInstance();
            $this->OnLoadState();
            $this->OnPostBackData();

            $_submit_handler = $this->request->getParam("_submit_handler");
            $_data_trackID = $this->request->getParam("_data_trackID");

            foreach (Array("_submit_handler", "_submit_source", "_submit_type", "_data_trackID") as $i => $value)
            {
                if (isset($_POST[$value]))
                    unset($_POST[$value]); if (isset($_GET[$value]))
                    unset($_GET[$value]);
            }

            if ($_submit_handler)
            {
                $ajaxResponse->addParam("_data_trackID", $_data_trackID);

                if (method_exists($this, $_submit_handler) === TRUE)
                {
                    try
                    {
                        if ($_submit_handler == "OnAjaxRequest")
                        {
                            call_user_func_array(Array($this, "OnAjaxRequest"), Array());
                        }
                        else
                        {
                            call_user_func_array(Array($this, $_submit_handler), Array());
                        }
                    }
                    catch (Exception $ex)
                    {
                        $this->OnPageException($ex);
                    }
                }
                else
                {
                    $error = "Event handler is not defined - " . $_submit_handler;
                    $this->addError($error);
                    errorLog($error);
                }

                $this->OnSaveState();
                $this->applyPossibleStateChange();
                $this->OnPageDestroy();

                echo $ajaxResponse->flush();
                exit;
            }
        }
    }

    private function handleFlashFileUpload()
    {
        if (!$this->isUploadAllowed() || count($_FILES) != 1)
        {
            echo "error:FILE_UPLOAD_UNEXPECTED_ERROR";
            return;
        }

        $fileName = @first(array_keys($_FILES));

        if (!$fileName)
        {
            echo "error:FILE_UPLOAD_UNEXPECTED_ERROR";
            return;
        }

        if ($_FILES[$fileName]["error"] == UPLOAD_ERR_OK)
        {
            $target = randomString(5) . "_" . $_FILES[$fileName]["name"];
            move_uploaded_file($_FILES[$fileName]["tmp_name"], PROTECTED_PERSISTENT_DIR . "tmp/" . $target);
            echo "complete:" . $target;
        }
        else if ($_FILES[$fileName]["error"] == UPLOAD_ERR_INI_SIZE || $_FILES[$fileName]["error"] == UPLOAD_ERR_FORM_SIZE)
        {
            echo "error:FILE_UPLOAD_EXCEED_THE_SERVER_FILE_SIZE_LIMIT";
        }
        else if (!isset($_FILES[$fileName]) || !isset($_FILES[$fileName]["name"]) || !$_FILES[$fileName]["name"] || $_FILES[$fileName]["size"] == 0)
        {
            echo "error:FILE_UPLOAD_UNEXPECTED_ERROR";
        }
        else if ($_FILES[$fileName]["error"] == UPLOAD_ERR_PARTIAL)
        {
            echo "error:FILE_UPLOAD_UNEXPECTED_ERROR";
        }
        else
        {
            echo "error:FILE_UPLOAD_UNEXPECTED_ERROR";
        }
    }

    private function buildAjaxControls($ajaxResponse)
    {
        $list = json_decode($this->request->getParam("controls"));

        for ($i = 0; $i < count($list); $i++)
        {
            $def = $list[$i];
            $args = Array();

            foreach ($def->rc_args as $k => $v)
            {
                $args[$k] = $v;
            }

            if ($def->rc_widget)
            {
                registerWidget($def->rc_widget);
            }

            $ctrl = TWEBCONTROL::createInstance($def->rc_name, $def->rc_phpclass, $args);
            $ajaxResponse->populate($def->rc_target, $ctrl);
        }
    }

    private function initializeWebControls($flag)
    {
        $object_vars = get_object_vars($this);

        foreach ($object_vars as $key => $value)
        {
            if (is_class($value, "TWebControl"))
            {
                $this->$key->setName($key);
                $this->$key->setContext($this);

                if ($flag)
                {
                    $this->$key->init();
                }
            }
        }
    }

    private function appendDataModelFromRequest()
    {
        $tmpList = $this->request->getParameters("/^___model_.*/");

        foreach ($tmpList as $key => $value)
        {
            $key = str_replace("___model_", "", $key);
            $this->model->setProperty($key, $value, TRUE, TRUE);
        }
    }

    protected function setKeepState($aValue)
    {
        $object_vars = get_object_vars($this);

        foreach ($object_vars as $key => $value)
        {
            if (is_class($value, "TWebControl"))
            {
                $this->$key->setKeepState($aValue);
            }
        }
    }

    public function OnLoadState()
    {
        $temp = $this->model->getProperty("__CONTROLSTATE");

        if ($temp)
        {
            $this->controlState->unserialize($temp);
        }

        $object_vars = get_object_vars($this);

        foreach ($object_vars as $key => $value)
        {
            if (is_class($value, "TWebControl"))
            {
                $this->$key->loadState();
            }
        }
    }

    protected function OnSaveState()
    {
        $object_vars = get_object_vars($this);

        foreach ($object_vars as $key => $value)
        {
            if (is_class($value, "TWebControl"))
            {
                $this->$key->saveState();
            }
        }

        $data = $this->controlState->serialize();

        if ($data)
        {
            $this->model->setProperty("__CONTROLSTATE", $data, TRUE, TRUE);
        }
    }

    protected function OnPostBackData()
    {
        $webControls = get_object_vars($this);

        foreach ($webControls as $key => $value)
        {
            if (is_class($value, "TWEBCONTROL"))
            {
                $this->$key->postBackData();
            }
        }
    }

    private function applyPossibleStateChange()
    {
        if ($this->fClass)
        {
            agent()->executeScript("page.applyClass(" . json_encode($this->fClass) . ")");
        }

        $styleProps = Array();

        if ($this->fStyle)
        {
            $styleProps = explodeStyleString($this->fStyle);
        }

        $tmp = $this->style->toArray();

        if (count($tmp) > 0)
        {
            $styleProps = array_merge($styleProps, $tmp);
        }

        if (count($styleProps) > 0)
        {
            agent()->executeScript("page.applyStyleProperties(" . json_encode($styleProps) . ")");
        }


        $webControls = get_object_vars($this);

        foreach ($webControls as $key => $value)
        {
            if (is_class($value, "TWEBCONTROL"))
            {
                $this->$key->applyPossibleStateChange();
            }
        }

        $this->OnPossibleStateChange();
    }

    protected function OnPossibleStateChange()
    {
        //
    }

    private function moveRepositoryFiles()
    {
        $devMode = TApplication::getInstance()->isDevMode();
        $subdirs = Array("css", "js");

        foreach ($subdirs as $ind => $subdir)
        {
            $srcDir = PROTECTED_REPO_DIR . "/" . $subdir . "/";
            $destDir = addSafeBackSlash($_SERVER['DOCUMENT_ROOT']) . "system/repository" . "/" . $subdir . "/";

            if ($devMode && is_dir($destDir))
            {
                TFileSystem::deleteDir($destDir);
            }

            if (!is_dir($destDir))
            {
                TFileSystem::copyDir($srcDir, $destDir);
            }
        }
    }

    public function OnPageDestroy()
    {
        
    }

    public function OnPageException($ex)
    {
        if (is_class($ex, "TDataValidatorException"))
        {
            return $this->showError($ex->getMessage());
        }
        else if (is_class($ex, "TDatabaseException"))
        {
            $this->showError("Database error. Check error.log");
        }
        else if (is_class($ex, "Exception"))
        {
            $this->showError($ex->getMessage());
        }

        errorLog($ex->getMessage());
        errorLog($ex->getTraceAsString());
    }

    public function finalize($output = TRUE)
    {
        $webControls = get_object_vars($this);

        foreach ($webControls as $key => $value)
        {
            if (is_class($value, "TWebControl"))
            {
                $this->$key->finalize();
            }
        }

        $this->applyPossibleStateChange();
        $this->moveRepositoryFiles();
        $this->OnPageDestroy();

        $codeString = "";
        $codeLines = $this->agent->OnLoad->getExecutableCode();

        while (list(, $code) = each($codeLines))
        {
            $codeString .= $code . ";";
        }

        if ($codeString)
        {
            $jsTag = "<script type='text/javascript'>\n//<![CDATA[\nfunction finalizePage(){{$codeString}}\n//]]>\n</script>";

            if ($output === TRUE)
            {
                echo $jsTag;
            }
            else
            {
                return $jsTag;
            }
        }

        return "";
    }

    public function loadLocaleFromFile($fileName, $locale = NULL, $onFailLoadEnglish = TRUE)
    {
        if ($locale === NULL)
        {
            $locale = $this->getLocale();
        }

        $file = PROTECTED_I18N_DIR . $locale . DIRECTORY_SEPARATOR . $fileName . ".i18n";

        if (file_exists($file))
        {
            $arr = parse_ini_file($file);

            while (list($k, $v) = each($arr))
            {
                TLOCATE::put($k, $v);
            }

            return;
        }

        if ($onFailLoadEnglish === TRUE && $locale != "en")
        {
            $this->loadLocaleFromFile($fileName, "en", FALSE);
        }
    }

    private function loadLocale($locale)
    {
        $this->loadLocaleFromFile("default");
    }

    public function setLocale($locale, $autoload = TRUE)
    {
        $this->session->setProperty("_____locale", $locale);

        if ($autoload === TRUE)
        {
            $this->loadLocale($locale);
        }
    }

    public function getLocale()
    {
        $locale = $this->session->getProperty("_____locale");

        if ($locale != NULL)
        {
            return strtolower($locale);
        }

        return "en";
    }

    public function setLookAndFeel($aLookAndFeel, $aStoreInSession = FALSE)
    {
        $this->fLookAndFeel = $aLookAndFeel;

        if ($aStoreInSession === TRUE)
        {
            $this->session->setProperty("___look_and_feel", $aLookAndFeel);
        }
    }

    public function getDirectLookAndFeel()
    {
        return $this->fLookAndFeel;
    }

    public function getLookAndFeel()
    {
        if ($this->fLookAndFeel)
        {
            return $this->fLookAndFeel;
        }

        $uri = $this->session->getProperty("___look_and_feel");

        if (!$uri)
        {
            $uri = $this->applicationContext->getLookAndFeel();
        }

        return $uri;
    }

    public function hasLookAndFeel()
    {
        $uri = $this->getLookAndFeel();
        return $uri ? TRUE : FALSE;
    }

    public function destroyWidget($aWidgetName)
    {
        $this->fDestroyWidgetList[] = $aWidgetName;
    }

    public function getDestroyWidgetList()
    {
        return $this->fDestroyWidgetList;
    }

    public function checkForTopPage()
    {
        agent()->executeScript("if(top.location.href != document.location.href) {top.location.href = document.location.href;}");
    }

    public function includeScript($path)
    {
        if (startsWith($path, "/") || startsWith($path, "http"))
        {
            //
        }
        else if (TFileSystem::isFile(PROTECTED_REPO_DIR . "js/" . $path))
        {
            $path = "/system/repository/js/" . $path;
        }

        if (!AJAX_REQUEST)
        {
            $this->head->addJavascriptUri($path);
        }
        else
        {
            response()->addScriptFile($path);
        }
    }

    public function includeStylesheet($path)
    {
        if (startsWith($path, "/") || startsWith($path, "http"))
        {
            //
        }
        else if (TFileSystem::isFile(PROTECTED_REPO_DIR . "css/" . $path))
        {
            $path = "/system/repository/css/" . $path;
        }

        if (!AJAX_REQUEST)
        {
            $this->head->addCssFile($path);
        }
        else
        {
            response()->addCssFile($path);
        }
    }

}

?>