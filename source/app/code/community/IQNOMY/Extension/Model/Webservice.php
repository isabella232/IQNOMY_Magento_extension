<?php

/**
 * IQNOMY Webservice
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Webservice
{
    const ENVIRONMENT_LIVE   = 'live';
    const ENVIRONMENT_TEST   = 'test';

    const SERVICE_TRACKER    = 'tracker';
    const SERVICE_MANAGEMENT = 'management';

    /**
     * @var array
     */
    protected $_serviceUrl = array(
        'live' => array(
            'tracker'    => 'https://liquifier.iqnomy.com/myliquidsuite-ws/rest/',
            'management' => 'https://management.iqnomy.com/myliquidsuite-ws/rest/'
        ),
        'test' => array(
            'tracker'    => 'https://liquifier.test.iqnomy.com/myliquidsuite-ws/rest/',
            'management' => 'https://management.test.iqnomy.com/myliquidsuite-ws/rest/'
        )
    );

    /**
     * @var string
     */
    protected $_environment;

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_tenantId;

    /**
     * @var string
     */
    protected $_apiKey;

    /**
     * cURL session.
     *
     * @var resource
     */
    protected $_curl;
    
    /**
     * Set webservice account details.
     *
     * @param string $environment
     * @param string $username
     * @param string $tenantId
     * @param string $apiKey
     */
    public function setAccountInfo($environment, $username, $tenantId, $apiKey)
    {
        if (!array_key_exists($environment, $this->_serviceUrl)) {
            Mage::throwException('Invalid environment configured.');
        }
        if ($username == '') {
            Mage::throwException('Empty username configured.');
        }
        if ($tenantId == '') {
            Mage::throwException('Empty tenant ID configured.');
        }
        if ($apiKey == '') {
            Mage::throwException('Empty API key configured.');
        }

        $this->_environment = $environment;
        $this->_username    = $username;
        $this->_tenantId    = $tenantId;
        $this->_apiKey      = $apiKey;
    }
    
    private $containersResponse = null;
    public function getContainers()
    {
        if($this->containersResponse == null)
        {
            $this->containersResponse = $this->_call(self::SERVICE_MANAGEMENT, 'containers/getcontainers');
        }

        if($this->containersResponse != null && is_array($this->containersResponse))
        {
            return $this->containersResponse;
        }
        return null;
    }
    
    public function getContainer($id)
    {
        $response = $this->_call(self::SERVICE_MANAGEMENT, 'containers/getcontainer?containerId='.$id);

        if($response != null && is_array($response))
        {
            return $response;
        }
        return null;
    }
    
    public function containerExists($id)
    {
        $response = $this->_call(self::SERVICE_MANAGEMENT, 'containers/getcontainer?containerId='.$id);
        if($response != null && is_array($response))
        {
            return true;
        }
        return false;
    }
    
    public function saveContainer($containerArray)
    {
        $this->containersResponse = null;
        
        return $this->_call(self::SERVICE_MANAGEMENT, 'containers/updatecontainer', $containerArray);
    }
    
    public function updateStatus()
    {	
	//Trying to update the status
	try
	{
	    $session = Mage::getSingleton('core/session');
	    if($session->getPlatformVersion() != Mage::getVersion() || $session->getConnectorVersion() != (string)Mage::getConfig()->getNode('modules/IQNOMY_Extension/version'))
	    {
		$array = array();
		$array["platform"] = "Magento";
		$array["platformVersion"] = Mage::getVersion();
		$array["connectorVersion"] = (string)Mage::getConfig()->getNode('modules/IQNOMY_Extension/version');
		$array["connectorCurrentStatus"] = "Connected";
		
		$this->_call(self::SERVICE_MANAGEMENT, 'connector/updatestatus', $array);
		$session->setPlatformVersion($array["platformVersion"]);
		$session->setConnectorVersion($array["connectorVersion"]);
	    }
	}
	catch (Exception $ex)
	{
	    
	}
    }
    
    public function deleteContainer($id)
    {
        $this->containersResponse = null;
        
        return $this->_call(self::SERVICE_MANAGEMENT, "containers/removecontainer?containerId=".$id, null, true, true);
    }
    
    private $liquidContentsResponse = null;
    public function getLiquidContents()
    {
        if($this->liquidContentsResponse == null)
        {
            $this->liquidContentsResponse = $this->_call(self::SERVICE_MANAGEMENT, 'liquidcontents/getliquidcontents');
        }
        
        if($this->liquidContentsResponse != null && is_array($this->liquidContentsResponse))
        {
            return $this->liquidContentsResponse;
        }
        return null;
    }
    
    private $containerLiquidContentsResponse = array();
    public function getLiquidContentsByContainer($containerId)
    {
	if(!array_key_exists($containerId, $this->containerLiquidContentsResponse))
        {
            $this->containerLiquidContentsResponse[$containerId] = $this->_call(self::SERVICE_MANAGEMENT, 'liquidcontents/getliquidcontents?containerId='.$containerId);
        }
        
        if($this->containerLiquidContentsResponse[$containerId] != null && is_array($this->containerLiquidContentsResponse[$containerId]))
        {
            return $this->containerLiquidContentsResponse[$containerId];
        }
        return array();
    }
    
    private $liquidContentResponses = array();
    public function getLiquidContent($id)
    {
        if(!array_key_exists($id, $this->liquidContentResponses))
        {
            $this->liquidContentResponses[$id] = $this->_call(self::SERVICE_MANAGEMENT, 'liquidcontents/getliquidcontent?liquidContentId='.$id);
        }
        
        if($this->liquidContentResponses[$id] != null && is_array($this->liquidContentResponses[$id]))
        {
            return $this->liquidContentResponses[$id];
        }
        return null;
    }
    
    public function liquidContentExists($id)
    {
        $response = $this->_call(self::SERVICE_MANAGEMENT, 'liquidcontents/getliquidcontent?liquidContentId='.$id);
        
        if($response != null && is_array($response))
        {
            return true;
        }
        return false;
    }
    
    public function saveLiquidContent($liquidContentArray)
    {
	$this->containerLiquidContentsResponse = array();
        $this->liquidContentResponses = array();
        $this->liquidContentsResponse = null;
        
        return $this->_call(self::SERVICE_MANAGEMENT, 'liquidcontents/updateliquidcontent', $liquidContentArray);
    }
    
    public function deleteLiquidContent($id)
    {
	$this->containerLiquidContentsResponse = array();
        $this->liquidContentResponses = array();
        $this->liquidContentsResponse = null;
        
        return $this->_call(self::SERVICE_MANAGEMENT, "liquidcontents/removeLiquidContent?liquidContentId=".$id, null, true, true);
    }
    
    private $dimensionsResponse = null;
    public function getDimensions()
    {
        if($this->dimensionsResponse == null)
        {
            $this->dimensionsResponse = $this->_call(self::SERVICE_MANAGEMENT, 'dimensions/getdimensions');
        }
        
        if($this->dimensionsResponse != null && is_array($this->dimensionsResponse))
        {
            return $this->dimensionsResponse;
        }
        return null;
    }
    
    

    /**
     * Create or update dimension.
     *
     * @param string $name
     * @param string $description
     * @param bool $active
     * @param array $properties
     * @return bool
     */
    public function updateDimension($name, $description, $active, $properties)
    {
        $this->dimensionsResponse = null;
        
        $dimensionProperty = array();
        foreach ($properties as $_property) {
            if ($_property['value'] !== '') {
                $dimensionProperty[] = array(
                    'value' => $_property['value'],
                    'label' => $_property['label'],
                );
            }
        }

        $response = $this->_call(self::SERVICE_MANAGEMENT, 'dimensions/updatedimension', array(
            'name'              => (string)$name,
            'description'       => (string)$description,
            'active'            => $active ? 'true' : 'false',
            'dimensionProperty' => $dimensionProperty
        ));

        return isset($response['result']) ? $response['result'] == 'true' : false;
    }

    /**
     * Get dimension details including properties.
     *
     * @param string $dimensionName
     * @return bool|array
     */
    public function getDimension($dimensionName)
    {
        return $this->_call(self::SERVICE_MANAGEMENT, 'dimensions/getdimension?dimensionName=' . urlencode($dimensionName));
    }

    /**
     * Track an event using the REST API.
     *
     * @param string $visitorId
     * @param string $followId
     * @param string $url
     * @param array $eventData
     * @param string|null $externalVisitorId
     * @return bool
     */
    public function trackEvent($visitorId, $followId, $url, $eventData, $externalVisitorId = null)
    {
        $queryParams = array(
            'tenant'      => $this->_tenantId,
            'vid'         => $visitorId,
            'fid'         => $followId,
            'iqurl'       => $url
        );

        if ($externalVisitorId) {
            $queryParams['externalvid'] = $externalVisitorId;
        }

        $data = array(
            'iqeventdata' => http_build_query($eventData)
        );

        $this->_call(self::SERVICE_TRACKER, 'trackevent/webshop?' . http_build_query($queryParams), $data, false);

        return true;
    }

    /**
     * Destructor. Closes cURL session and frees all resources.
     */
    public function __destruct()
    {
        if (isset($this->_curl)) {
            curl_close($this->_curl);
            unset($this->_curl);
        }
    }

    /**
     * Call the webservice and return the response.
     *
     * @param string $service self::SERVICE_MANAGEMENT or self::SERVICE_TRACKER
     * @param string $method
     * @param array $data Optional. If not empty, a POST-request will be made.
     * @param bool $postJSON Send data as JSON instead of formdata.
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _call($service, $method, $data = null, $postJSON = true, $isDelete = false)
    {
        //WARNING: Zend_Json usage has been removed due to errors on encoding euro signs etc.
        Varien_Profiler::start(__METHOD__);

        if (empty($this->_serviceUrl[$this->_environment][$service])) {
            Varien_Profiler::stop(__METHOD__);
            Mage::throwException('Invalid service specified.');
        }

        // initialize cURL session
        // WARNING! Keeping open the session will malform the next request.
        //if (!$this->_curl) {
            $this->_curl = curl_init();
        //}

    	$serverId = Mage::getSingleton('core/session')->getRestServerId();
    	if($serverId == null)
    	{
    	    $servers = array("web1", "web2");
    	    $serverId = $servers[array_rand($servers, 1)];
    	    Mage::getSingleton('core/session')->setRestServerId($serverId);
    	}

        // prepare cURL options
        $options = array(
            CURLOPT_POST           => false,
            CURLOPT_URL            => $this->_serviceUrl[$this->_environment][$service] . $method,
            CURLOPT_USERPWD        => sprintf('%s*%s:%s', $this->_username, $this->_tenantId, $this->_apiKey),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 30,
	        CURLOPT_COOKIE	        => 'SERVERID='.$serverId,
            CURLOPT_HTTPHEADER     => array(
                'Accept: application/json',
                'User-Agent: ' . sprintf(
                    'Magento/%s IQNOMY_Extension/%s',
                    Mage::getVersion(),
                    (string)Mage::getConfig()->getNode('modules/IQNOMY_Extension/version')
                )
            )
        );

        if(Mage::getSingleton('adminhtml/session')->getData('use_api_secure') === null)
        {
            Mage::getSingleton('adminhtml/session')->setData('use_api_secure', true);
        }

        if(Mage::getSingleton('adminhtml/session')->getData('use_api_secure'))
        {
            $options[CURLOPT_SSL_VERIFYPEER] = true;
        }
        else
        {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }

        if (is_null($data) && !$isDelete) {
            $this->_log("GET {$options[CURLOPT_URL]}");
        }
        elseif($isDelete) {
            $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
            $this->_log("DELETE {$options[CURLOPT_URL]}");
        }
        else {
            $options[CURLOPT_POST] = true;
            if ($postJSON) {
                $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
                $this->_log("POST (json) {$options[CURLOPT_URL]}\n" . json_encode($data));
                //$options[CURLOPT_POSTFIELDS] = Zend_Json::encode($data);
                //$this->_log("POST (json) {$options[CURLOPT_URL]}\n" . Zend_Json::encode($data));
            }
            else {
                $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
                $options[CURLOPT_POSTFIELDS] = http_build_query($data);
                $this->_log("POST (formdata) {$options[CURLOPT_URL]}\n" . http_build_query($data));
            }
        }
        curl_setopt_array($this->_curl, $options);
	
        // perform the request
        $timer = microtime(true);
        $responseBody = curl_exec($this->_curl);
	
        $timer = microtime(true) - $timer;
        if ($responseBody === false) {
            Varien_Profiler::stop(__METHOD__);
            $error = curl_error($this->_curl);
            curl_close($this->_curl);
            unset($this->_curl);

            //Mage::getSingleton('adminhtml/session')->setData('use_api_secure', true)
            if(Mage::getSingleton('adminhtml/session')->getData('use_api_secure'))
            {
                //This call was performed with verfify_peer = true but it failed, maybe the openssl or something is to old, let's try it without.
                Mage::getSingleton('adminhtml/session')->setData('use_api_secure', false);
                return $this->_call($service, $method, $data, $postJSON, $isDelete);
            }
            else
            {
                //The call was performed with verify_peer = false but it failed, so setting verify_peer to false was not to solution.
                Mage::getSingleton('adminhtml/session')->setData('use_api_secure', true);
                Mage::throwException('IQNOMY webservice connection failure: ' . $error);
            }
        }

        // log times
        $this->_log(sprintf(
            "Request duration: %0.2fs (DNS: %0.2fs | Connect: %0.2fs | Wait: %0.2fs)",
            $timer,
            curl_getinfo($this->_curl, CURLINFO_NAMELOOKUP_TIME),
            curl_getinfo($this->_curl, CURLINFO_CONNECT_TIME) - curl_getinfo($this->_curl, CURLINFO_NAMELOOKUP_TIME),
            curl_getinfo($this->_curl, CURLINFO_STARTTRANSFER_TIME) - curl_getinfo($this->_curl, CURLINFO_CONNECT_TIME)
        ));

        // process result
        $httpCode = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
        $this->_log("HTTP $httpCode\n$responseBody");
        if ($httpCode < 200 || $httpCode >= 400) {
            // extract errormessage from response body
            if (preg_match('/<b>description<\/b>(.*)<\/p>/Uis', $responseBody, $match)) {
                $description = html_entity_decode($match[1]);
            }
            else {
                $description = 'Unknown error';
                try {
                    //$result = Zend_Json_Decoder::decode($responseBody);
                    $result = json_decode($responseBody, true);
                    if (isset($result['message'])) {
                        $description = $result['message'];
                    }
                }
                catch (Exception $exception) {
                }
            }
            $this->_log("IQNOMY webservice error ($httpCode): $description", Zend_Log::WARN);
            Varien_Profiler::stop(__METHOD__);
            curl_close($this->_curl);
            unset($this->_curl);
            Mage::throwException("IQNOMY webservice error ($httpCode): $description");
        }

        //$result = Zend_Json_Decoder::decode($responseBody);
        $result = json_decode($responseBody, true);

        Varien_Profiler::stop(__METHOD__);
        curl_close($this->_curl);
	    unset($this->_curl);
	
	if($method != 'connector/updatestatus')
	{
	    $this->updateStatus();
	}
	
        return $result;
    }

    /**
     * Log to IQNOMY logfile.
     *
     * @param string $message
     * @param integer $level
     */
    protected function _log($message, $level = null)
    {        
        if (is_null($level)) {
            $level = Zend_Log::DEBUG;
        }
        $forceLog = Mage::getStoreConfigFlag('iqnomy_extension/account/enable_logging');

        Mage::log($message, $level, 'iqnomy.log', $forceLog);
    }
}
