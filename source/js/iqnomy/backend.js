/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
var currentOnLoad = window.onload;
window.onload = function()
{
    if(typeof currentOnLoad == 'function' && currentOnLoad != null)
    {
        currentOnLoad();
    }
    
    var animationType = document.getElementById("animation_type");
    if(typeof animationType != "undefined" && animationType != null)
    {
        animationTypeChanged(animationType);
    }
    
    var productSelector = document.getElementById("product_id");
    if(typeof productSelector != "undefined" && productSelector != null)
    {
        productChanged(productSelector);
    }
    
    var imageSelector = document.getElementById("image");
    if(typeof imageSelector != "undefined" && imageSelector != null)
    {
        var tr = findParentByTag(imageSelector, "tr");
        tr.style.display = "none";
    }
    
    var chooser = document.getElementById("productChooser_filter_chooser_name");
    if(typeof chooser != "undefined" && chooser != null)
    {
        productChooserJsObject.doFilter();
    }
};

function findParentByTag(element, tag)
{
    if(element == null || typeof tag == "undefined")
    {
        return null;
    }
    
    var parent = element.parentNode;
    if(typeof parent.tagName == "undefined")
    {
        return null;
    }
    
    if(parent.tagName.toLowerCase() == tag.toLowerCase())
    {
        return parent;
    }
    
    return findParentByTag(parent, tag);
}

function animationTypeChanged(element)
{
    var value = element.value;
    if(value == "3")
    {
        hideTextField(document.getElementById("duration"));
        hideTextField(document.getElementById("pause"));
    }
    else
    {
        showTextField(document.getElementById("duration"));
        showTextField(document.getElementById("pause"));
    }
}

function productChanged(element, fromProductChooser)
{
    var value = element.value;
    if(value != "")
    {
        hideTextField(document.getElementById("image-buttons"));
        hideTextField(document.getElementById("url"));
        hideTextField(document.getElementById("title"));
    }
    else
    {
        showTextField(document.getElementById("image-buttons"));
        showTextField(document.getElementById("url"));
        showTextField(document.getElementById("title"));
    }
    
    if(typeof fromProductChooser == 'undefined' || (fromProductChooser && value != ""))
    {
	var message = document.getElementById("product-no-images-message");
	if(message != null)
	{
	    message.style.display = "none";
	}
    }
}

function showTextField(element)
{
    var tr = findParentByTag(element, "tr");
    if(tr != null)
    {
        element.className = element.className.replace(/disabled-class-/g, "");
        tr.style.display = "";
    }
}

function hideTextField(element)
{    
    var tr = findParentByTag(element, "tr");
    if(tr != null)
    {
        var classes = element.className.split(" ");
        for(var i in classes)
        {
            if(typeof classes[i] == "string")
            {
                if(classes[i].indexOf("disabled-class-") === -1)
                {
                    classes[i] = "disabled-class-" + classes[i];
                }
            }
        }
        element.className = classes.join(" ");
        tr.style.display = "none";
    }
}

function closeProductChooser()
{
    document.getElementById("product-chooser").style.display = "none";
}

function openProductChooser()
{
    document.getElementById("product-chooser").style.display = "block";
}

function closeImageChooser()
{
    document.getElementById("image-chooser").style.display = "none";
}

function openImageChooser()
{
    document.getElementById("image-chooser").style.display = "block";
}

var productChooser = {};
productChooser.setElementValue = function(value)
{
    var items = value.split("/");
    var productId = items[1];
    
    var select = document.getElementById("product_id");
    select.value = productId;

    if(select.value == "")
    {
	var message = document.getElementById("product-no-images-message");
	if(message != null)
	{
	    message.style.display = "block";
	}
    }

    productChanged(select, true);

    closeProductChooser();
};

function updateImageLabel()
{
    var imageName = document.getElementById('image').value;
    
    while(imageName.indexOf('\/') >= 0)
    { 
        imageName = imageName.substring(imageName.indexOf('\/') + 1); 
    } 
    
    while(imageName.indexOf('\\') >= 0)
    {
        imageName = imageName.substring(imageName.indexOf('\\') + 1);
    }
    document.getElementById('current-file').innerHTML = imageName;
    document.getElementById('current-file').style.display = "";
    
    var currentImage = document.getElementById('current-image');
    if(typeof currentImage != "undefined" && currentImage != null)
    {
        currentImage.parentNode.style.display = "none";
    }
    
    document.getElementById("existing-image").value = "";
}

function selectImage(image, url)
{
    var imageHolder = document.getElementById("current-image");
    imageHolder.src = url;
    imageHolder.parentNode.href = url;
    imageHolder.parentNode.style.display = "";
    
    document.getElementById('current-file').style.display = "none";
    document.getElementById('image').value = "";
    document.getElementById("existing-image").value = image;
    
    closeImageChooser();    
}