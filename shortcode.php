<?php
    /**
     * This is the shortcut to DIRECTORY_SEPARATOR
     */
    defined('DS') or define('DS',DIRECTORY_SEPARATOR);
    defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));
     
    /**
     * This is the shortcut to Yii::app()
     */
    function app()
    {
        return Yii::app();
    }
     
    /**
     * This is the shortcut to Yii::app()->clientScript
     */
    function cs()
    {
        // You could also call the client script instance via Yii::app()->clientScript
        // But this is faster
        return Yii::app()->getClientScript();
    }
     
    /**
     * This is the shortcut to Yii::app()->user.
     */
    function user() 
    {
        return Yii::app()->getUser();
    }
     
    /**
     * This is the shortcut to Yii::app()->createAbsoluteUrl()
     */
    function absUrl($route,$params=array(),$schema='',$ampersand='&')
    {
        return Yii::app()->createAbsoluteUrl($route,$params,$schema,$ampersand);
    }
   
    /**
     * This is the shortcut to Yii::app()->createUrl()
     */
    function url($route,$params=array(),$ampersand='&')
    {
        return Yii::app()->createUrl($route,$params,$ampersand);
    }
     
    /**
     * This is the shortcut to CHtml::encode
     */
    function h($text)
    {
        return htmlspecialchars($text,ENT_QUOTES,Yii::app()->charset);
    }
     
    /**
     * This is the shortcut to CHtml::link()
     */
    function l($text, $url = '#', $htmlOptions = array()) 
    {
        return CHtml::link($text, $url, $htmlOptions);
    }
     
    /**
     * This is the shortcut to Yii::t() with default category = 'stay'
     */
    function t($message, $category = 'stay', $params = array(), $source = null, $language = null) 
    {
        return Yii::t($category, $message, $params, $source, $language);
    }
     
    /**
     * This is the shortcut to Yii::app()->request->baseUrl
     * If the parameter is given, it will be returned and prefixed with the app baseUrl.
     */
    function bu($url=null) 
    {
        if (substr($url, 0, 3) == 'http') {
            static $baseUrl;
            if ($baseUrl===null)
                $baseUrl=Yii::app()->getRequest()->getBaseUrl();
            return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');            
        } else {
            if (substr($url, 0, 1) == '/') {
                return substr($url, 1, strlen($url));
            } else {
                return $url;   
            }
        }
    }
     
    /**
     * Returns the named application parameter.
     * This is the shortcut to Yii::app()->params[$name].
     */
    function param($name) 
    {
        $return = Yii::app()->params[$name];
        if (is_array($return)) {
            return (object) $return;
        } 
        return $return;
    }

    /**
     * Returns session object.
     * This is the shortcut to Yii::app()->session.
     */
    function session($name = NULL, $value = NULL) 
    {
        if (!empty($value) && !empty($name)) {
            Yii::app()->session[$name] = $value;   
        } else if (empty($value) && !empty($name)) {
            return Yii::app()->session[$name];
        } else {
            return Yii::app()->session;
        }
    }

    /**
     * Returns request type.
     * This is the shortcut to Yii::app()->request
     */
    function request(){
        return Yii::app()->request;
    }

    /**
     * Returns host info for application.
     * This is the shortcut to Yii::app()->request->hostInfo.
     */
    function base_url($path = NULL)
    {
        return Yii::app()->request->hostInfo . bu($path);
    }

    /**
     * This is the shortcut to Yii::app()->theme->baseUrl.
     */    
    function theme_url($url = NULL) {
        return Yii::app()->theme->baseUrl . $url;
    }
    
    /**
     * This is the shortcut to Yii::app()->getModule('moduleId').
     */ 
    function getModule($moduleId = NULL) {
        return Yii::app()->getModule($moduleId);
    }


    function base_path() {
        return dirname(Yii::app()->request->scriptFile);
    }

    function user_profile()
    {
        return Yii::app()->user->user_profile;
    }

    /**
     * This is function to get excerpt for long text.
     */        
    function get_excerpt($str, $char = NULL, $more_text = '[...]')
    {
        $maxLength = ($char) ? $char : 250;
        $strResult = strip_tags($str);
        $result = substr($strResult, 0, $maxLength);
        
        if (strlen($strResult) <= $maxLength) {
        
            return $result;
            
        } else {
        
            return $result . ' ' . $more_text;
        
        }
    }

    /**
     * Check strpos with array needle
     */        
    function strpos_arr($haystack, $needle) {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $what) {
            if(($pos = strpos($haystack, $what))!==false) return $pos;
        }
        return false;
    }

    /**
     * This is the shortcut to Yii::app()->translate
     */
    function translate() {
        return Yii::app()->translate;
    }