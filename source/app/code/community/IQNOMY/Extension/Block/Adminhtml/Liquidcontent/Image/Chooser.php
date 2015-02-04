<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Image_Chooser extends Mage_Core_Block_Abstract 
{
    public function __construct($data)
    {
        parent::__construct($data);
    }
    
    public function _toHtml()
    {        
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('iqnomy_extension')->__("Close"),
            'onclick' => 'closeImageChooser();',
            'class' => 'delete',
            'style' => 'float: right; padding-top: 2px;'
        ));
        
	$images = array_merge($this->getFilesRecursive(Mage::getBaseDir("media") . "/iqnomy"), $this->getFilesRecursive(Mage::getBaseDir("media") . "/ibanners"));
	
        $html = '<div id="image-chooser" class="entry-edit" style="display: none; position: fixed; left: 0px; right: 0px; top: 0px; bottom: 0px; z-index: 500; background-color: rgba(0,0,0,0.75); padding: 10%;overflow: auto;">
                    <div class="entry-edit-head">
                        <h4 class="icon-head head-edit-form fieldset-legend" style="margin-top: 2px;">'.Mage::helper('iqnomy_extension')->__("Search image").'</h4>
                        '.$button->toHtml().'
                    </div>
                    <div class="fieldset">';
        
	if(count($images) > 0)
	{
	    $html .= '<div class="grid">';
		$html .= '<div class="hor-scroll">';
		    $html .= '<table cellspacing="0" class="data">';
			$html .= '<colgroup>';
			    $html .= '<col width="90">';
			    $html .= '<col>';
			    $html .= '<col width="100">';
			$html .= '</colgroup>';
			$html .= '<thead>';
			    $html .= '<tr class="headings">';
				$html .= '<th><span class="nobr">'.Mage::helper('iqnomy_extension')->__("Image").'</span></th>';
				$html .= '<th><span class="nobr"></span></th>';
				$html .= '<th class="last"><span class="nobr"></span></th>';
			    $html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			    $even = true;
			    foreach($images as $image)
			    {
				$url = Mage::helper('iqnomy_extension')->getBaseUrl('media') . "/" . $image;
				
				$html .= '<tr class="'.($even ? "even " : "").'pointer">';
				    $html .= '<td><img style="max-width: 200px; max-height: 60px;" src="'.$url.'"></td>';
				    $html .= '<td><a href="'.$url.'" target="_blank">Preview</a></td>';
				    $html .= "<td class='a-center last'><a href='javascript:void(0);' onclick='selectImage(\"".$image."\", \"".$url."\");'>".Mage::helper('iqnomy_extension')->__("Select")."</a></td>";
				$html .= '</tr>';
				
				$even = !$even;
			    }
			$html .= '</tbody>';
		    $html .= '</table>';
		$html .= '</div>';
	    $html .= '</div>';
	}
	else
	{
	    $html .= Mage::helper('iqnomy_extension')->__("No images were found.");
	}
	
	
	$html .= '</div>
                </div>';
	
	return $html;
    }
    
    private $allowedExtensions = array(
	"png",
	"gif",
	"jpg",
	"jpeg"
    );
    
    private function getFilesRecursive($dir)
    {
	$files = array();
	foreach(scandir($dir) as $item)
	{
	    if($item != "." && $item != "..")
	    {
		if(is_dir($dir . "/" . $item))
		{
		    $files = array_merge($files, $this->getFilesRecursive($dir . "/" . $item));
		}
		else
		{
		    $file = str_replace(Mage::getBaseDir("media"), "", $dir . "/" . $item);
		    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		    if(in_array($ext, $this->allowedExtensions))
		    {
			$files[] = $file;
		    }
		}
	    }
	}
	return $files;
    }
}
