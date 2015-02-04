/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

//Array with all banners.
var iqBanners = new Array();
var iqProductSelections = new Array();

var iqNewsletterObject = null;
var iqCallbackCalled = false;
var iqCallbackLastImpression = null;

function iqNewsletter(iqContainerId, isIqnomyBanner, enabled)
{
    this.iqContainerId = iqContainerId;
    this.isIqnomyBanner = isIqnomyBanner;
    this.enabled = enabled;
    this.containerImpression = null;
    
    this.process = function()
    {
	try
	{
	    var impression = this.containerImpression;
	    if(typeof impression.imprElements != 'undefined' && typeof impression.container !== 'undefined')
	    {
		var impressionElement = impression.imprElements.pop();
		if(typeof impressionElement.htmlTemplate == 'string')
		{
		    var content = IQJquery.parseJSON(impressionElement.htmlTemplate);
		}
		else
		{
		    var content = impressionElement.htmlTemplate;
		}
		if(typeof content != "undefined" && typeof content.html == "string")
		{
		    var height = parseInt(content.image_height);
		    var width = parseInt(content.image_width);

		    var html = content.html;

		    html = iqReplaceAllOccurences(html, "${popup_width}", width + 20);
		    html = iqReplaceAllOccurences(html, "${popup_width_half}", (width + 20) / 2);
		    html = iqReplaceAllOccurences(html, "${popup_height_half}", (height + 100) / 2);
		    html = iqReplaceAllOccurences(html, "${popup_coloration}", content.popup_coloration);
		    html = iqReplaceAllOccurences(html, "${popup_close_icon_color}", content.popup_close_icon_color);
		    html = iqReplaceAllOccurences(html, "${popup_title}", content.popup_title);
		    html = iqReplaceAllOccurences(html, "${popup_imageurl}", content.image);
		    html = iqReplaceAllOccurences(html, "${popup_alttext}", content.popup_alttext);
		    html = iqReplaceAllOccurences(html, "${webshop_url}", content.webshop_url);
		    html = iqReplaceAllOccurences(html, "${popup_submit_text}", content.popup_submit_text);

		    if(typeof iqIsPreview == 'undefined' || !iqIsPreview)
		    {
			if(typeof impression.preInjectionScript == 'string')
			{
			    impression.preInjectionScript = iqReplaceAllOccurences(impression.preInjectionScript, "${containerid}", impression.container.id);
			    eval(impression.preInjectionScript);
			}
		    }

		    var htmlId = 'IQ' + impression.container.name;
		    htmlId = iqReplaceAllOccurences(htmlId, ' ', '');
		    htmlId = iqReplaceAllOccurences(htmlId, '-', '');
		    htmlId = '_iqContentBox' + htmlId;
		    
		    if(typeof iqIsPreview != 'undefined' && iqIsPreview)
		    {
			IQJquery("body").append(IQJquery("<div id='" + htmlId + "'></div>"));
		    }

		    IQJquery("#" + htmlId).append(IQJquery(html));

		    if(typeof iqIsPreview == 'undefined' || !iqIsPreview)
		    {
			if(typeof impression.postInjectionScript == 'string')
			{
			    impression.postInjectionScript = iqReplaceAllOccurences(impression.postInjectionScript, "${containerid}", impression.container.id);
			    eval(impression.postInjectionScript);
			}
		    } 
		}
	    }
	}
	catch(ex)
	{
	    console.log(ex);
	    //Some error occurred.
	}
	
//	console.log(this.containerImpression);
    };
}

function iqProductSelection(htmlId, iqContainerId, enabled, url)
{
    this.htmlId = htmlId;
    this.enabled = enabled;
    this.iqContainerId = iqContainerId;
    this.productIds = {};
    this.hasProducts = false;
    this.url = url;
    
    this.addProductId = function(productId, order)
    {
	this.hasProducts = true;
        this.productIds[order] = productId;
    };
    
    this.process = function()
    {
	if(this.hasProducts)
	{
	    var productSelection = this;
	    IQJquery.ajax({
		url : this.url,
		type: 'POST',
		dataType: 'json',
		data: { product_ids: this.productIds }
	    }).done(function (data) {
		if(typeof data != 'undefined' && typeof data.success != 'undefined' && data.success)
		{
		    data.grid += '<div class="moo"><div class="toolbar"></div></div>';
		    var html = IQJquery(data.grid);
		    html.find('div.toolbar').hide();
		    html.animate({ opacity: 0 }, 0);
		    IQJquery("#" + productSelection.htmlId).append(html);
		    html.animate({ opacity: 1 }, 200);
		}
	    });
	}
    };
}

function iqBanner(htmlId, iqContainerId, height, animationType, duration, pause, isIqnomyBanner, enabled)
{
    this.htmlId = htmlId;
    this.iqContainerId = iqContainerId;
    this.height = height;
    this.animationType = animationType;
    this.isIqnomyBanner = isIqnomyBanner;
    this.enabled = enabled;
    
    if(this.animationType != 'none')
    {
        this.duration = parseInt(duration);
        this.pause = parseInt(pause);
    }
    
    this.contents = new Array();
    
    this.addContent = function(content, order)
    {
        this.contents[order] = content;
    };
    
    this.hasContent = function()
    {
        return (this.contents.length > 0);
    };
    
    this.getImpressContainer = function()
    {
        return {id: this.iqContainerId};
    };
    
    this.isLoaded = function()
    {
        //Checking if all images of the banner are loaded.
        var loaded = true;
        IQJquery("#" + this.htmlId).find("img").each(function()
        {
            if(!IQJquery(this).get(0).complete)
            {
                loaded = false;
            }
        });
        return loaded;
    };
}

function iqAddProductSelection(htmlId, iqContainerId, enabled, url)
{
    var element = document.getElementById(htmlId);
    //Checking if banner element exists on this page.
    if(element == null || typeof element == 'undefined')
    {
	enabled = false;
    }
    
    iqProductSelections.push(new iqProductSelection(htmlId, iqContainerId, enabled, url));
}

function iqAddBanner(htmlId, iqContainerId, height, animationType, duration, pause, isIqnomyBanner, enabled, isNewsletter)
{
    if(!isIqnomyBanner && !isNewsletter)
    {
	var element = document.getElementById(htmlId);
	//Checking if banner element exists on this page.
	if(element == null || typeof element == 'undefined')
	{
	    enabled = false;
	}
    }
    
    if(isNewsletter)
    {
	iqNewsletterObject = new iqNewsletter(iqContainerId, isIqnomyBanner, enabled);
    }
    else
    {
	iqBanners.push(new iqBanner(htmlId, iqContainerId, height, animationType, duration, pause, isIqnomyBanner, enabled));
    }
}

function iqGetBanner(iqContainerId)
{
    //Getting banner by iqnomy container id.
    for(var i in iqBanners)
    {
        if(iqBanners[i].iqContainerId == iqContainerId)
        {
            return iqBanners[i];
        }
    }
    return null;
}

function iqGetProductSelection(iqContainerId)
{
    for(var i in iqProductSelections)
    {
	if(iqProductSelections[i].iqContainerId = iqContainerId)
	{
	    return iqProductSelections[i];
	}
    }
    return null;
}

function prepareContentUrl(url, lqcid)
{
    lqcid = encodeURIComponent(lqcid);
    var urlElement = document.createElement("a");
    urlElement.href = url;   
    return urlElement.href.replace(urlElement.hash, "") + (urlElement.search.indexOf("?") === -1 ? "?" : "&") + "lqcid=" + lqcid + urlElement.hash;
}

function iqContentToProductSelection(iqContainerId, jsonContent, order, ignoreEnabled)
{
    try
    {
        var productSelection = iqGetProductSelection(iqContainerId);
        if(productSelection != null)
        {
            if(productSelection.enabled || ignoreEnabled)
            {
		if(typeof jsonContent != "object")
		{
		    var content = IQJquery.parseJSON(jsonContent);
		}
		else
		{
		    var content = jsonContent;
		}

		productSelection.addProductId(content.product_id, order);
            }
            return true;
        }
    }
    catch(exception)
    {
	//alert(exception);
        //An exception, invalid content.
    }
    return false;
}

function iqContentToBanner(iqContainerId, jsonContent, order, lqcid, ignoreEnabled)
{
    try
    {
        var banner = iqGetBanner(iqContainerId);
        if(banner != null)
        {
            if(banner.enabled || ignoreEnabled)
            {
		if(typeof jsonContent != "object")
		{
		    var content = IQJquery.parseJSON(jsonContent);
		}
		else
		{
		    var content = jsonContent;
		}

                if(banner.isIqnomyBanner)
                {
                    if(typeof content.iqnomy_banner_value_id != "undefined")
                    {
			IQJquery(document).ready(function()
			{
			    var iqnomyContent = {};
			    iqnomyContent.slideId = content.iqnomy_banner_value_id;
			    var searchString = "#" + banner.htmlId + "-" + content.iqnomy_banner_value_id;
			    searchString = searchString.replace("iqnomy-", "");
			    iqnomyContent.html = IQJquery(searchString);
			    IQJquery(searchString).addClass("iqnomy-prepared");
			    
			    if(typeof iqnomyContent.html != "undefined" && iqnomyContent.html.length)
			    {
				banner.addContent(iqnomyContent, order);
			    }
			});
                    }
                }
                else
                {
                    if(typeof content.image != "string")
                    {
                        content.image = "";
                    }

                    if(typeof content.title != "string")
                    {
                        content.title = "";
                    }

                    if(typeof content.url == "string")
                    {
                        if(typeof lqcid != "undefined")
                        {
                            content.url = prepareContentUrl(content.url, lqcid);
                        }
                    }
                    else
                    {
                        content.url = "";
                    }

                    if(typeof content.price != "string")
                    {
                        content.price = "";
                    }

                    if(typeof content.alt_text != "string")
                    {
                        content.alt_text = content.title;
                    }

                    if(typeof content.url_target != "string")
                    {
                        content.url_target = "";
                    }
		    
		    if(typeof content.special_price != "string")
		    {
			content.special_price = content.price;
		    }

		    if(!((content.special_price_from_date == null || content.special_price_from_date === 0 || content.special_price_from_date <= IQUnixTime) && (content.special_price_to_date == null || content.special_price_to_date === 0 || content.special_price_to_date > IQUnixTime)))
		    {
			content.special_price = content.price;
		    }

		    for(var varName in content)
		    {
			if(varName != "html" && varName != "additional_variables" && varName != "iqnomy_banner_value_id" && typeof content[varName] == 'string')
			{
			    if(varName == "image")
			    {
				var templateName = "imageUrl";
			    }
			    else if(varName == "url_target")
			    {
				var templateName = "target";
			    }
			    else if(varName == "alt_text")
			    {
				var templateName = "alt";
			    }
			    else
			    {
				var templateName = iqCreateCamelCase(varName);
			    }

			    if(templateName != null)
			    {
				content.html = iqReplaceAllOccurences(content.html, '${' + templateName + '}', content[varName]);
			    }
			}
		    }
		    
		    if(typeof content.additional_variables != "undefined")
		    {
			for(var varName in content.additional_variables)
			{
			    if(typeof content.additional_variables[varName] == 'string')
			    {
				var templateName = iqCreateCamelCase(varName);
				if(templateName != null)
				{
				    content.html = iqReplaceAllOccurences(content.html, '${' + templateName + '}', content.additional_variables[varName]);
				}
			    }
			}
		    }
		    
                    banner.addContent(content, order);
                }
            }
            return true;
        }
    }
    catch(exception)
    {
	//alert(exception);
        //An exception, invalid content.
    }
    return false;
}

function iqReplaceAllOccurences(subject, search, replace)
{
    while(subject.indexOf(search) >= 0)
    {
	subject = subject.replace(search, replace);
    }
    return subject;
}

function iqCreateCamelCase(string)
{
    var camelCased = "";
    var parts = string.split("_");
    for(var i in parts)
    {
	var part = parts[i];
	if(part.length > 0 && typeof part == 'string')
	{
	    camelCased += part.charAt(0).toUpperCase();
	    camelCased += part.slice(1);
	}
    }
    if(camelCased.length > 0)
    {
	return camelCased.charAt(0).toLowerCase() + camelCased.slice(1);
    }
    return null;
}

//Function to get all banners as objects with only an id-field.
function iqGetBannerContainers()
{
    var containers = new Array();
    var addedIds = {};
    for(var i in iqBanners)
    {
        if(typeof iqBanners[i] === 'object' && typeof iqBanners[i].getImpressContainer === 'function')
        {
            var impressContainer = iqBanners[i].getImpressContainer();
            if(!(impressContainer.id in addedIds))
            {
                containers.push(impressContainer);
                addedIds[impressContainer.id] = impressContainer.id;
            }
        }
    }
    return containers;
}

function iqImpressCallback(impression)
{
    iqCallbackCalled = true;
    iqCallbackLastImpression = impression;
    
    if(typeof impression != 'undefined')
    {
        var d = new Date();
        d.setDate(d.getDate() + 365*2);

        if (typeof impression.visitorId != 'undefined' && impression.visitorId != null ) {
                var visitorCookieStr = "_iqnomyvid=" + impression.visitorId + "; path=/; ";
                visitorCookieStr += "expires=" + d.toGMTString() + "; ";
                //visitorCookieStr += "domain=" + d + ";";
                document.cookie = visitorCookieStr;
        } 
        if (typeof impression.followId != 'undefined' && impression.followId != null ) {
                var visitorCookieStr = "_iqnomyfid=" + impression.followId + "; path=/; ";
                visitorCookieStr += "expires=" + d.toGMTString() + "; ";
                //visitorCookieStr += "domain=" + d + ";";
                document.cookie = visitorCookieStr;
        } 
    }
    
    if(typeof impression != 'undefined' && "containerImpressions" in impression && typeof impression.containerImpressions != 'undefined' && impression.containerImpressions.length > 0)
    {
        for(var i in impression.containerImpressions)
        {
            var containerImpression = impression.containerImpressions[i];
            if(typeof containerImpression == 'object' && "container" in containerImpression && "imprElements" in containerImpression && typeof containerImpression.imprElements != 'undefined' && containerImpression.imprElements.length > 0)
            {
                var containerImpressionUsed = false;
                
                for(var x in containerImpression.imprElements)
                {
                    var impressionElement = containerImpression.imprElements[x];
                    if(typeof impressionElement == 'object' && "htmlTemplate" in impressionElement && "order" in impressionElement)
                    {
                        //Adding the returned content to the correct banner.
                        if(iqContentToBanner(containerImpression.container.id, impressionElement.htmlTemplate, impressionElement.order, impressionElement.lqcid))
                        {
                            containerImpressionUsed = true;
                        }
			else if(iqContentToProductSelection(containerImpression.container.id, impressionElement.htmlTemplate, impressionElement.order))
			{
			    containerImpressionUsed = true;
			}
			
			if(iqNewsletterObject != null && iqNewsletterObject.iqContainerId == containerImpression.container.id)
			{
			    iqNewsletterObject.containerImpression = containerImpression;
			    containerImpressionUsed = true;
			}
                    }
                }
                
                if(!containerImpressionUsed)
                {
                    IQImpressor.defaultInjectContainerImpression(containerImpression);
                }
            }
        }

	iqFinalize(false);
	
	IQJquery(document).ready(function()
	{
	    iqFinalize(true);
	});
    }
}

function iqFinalize(iqnomy)
{
    for(var y in iqBanners)
    {
	var banner = iqBanners[y];
	if(banner.enabled && banner.isIqnomyBanner == iqnomy)
	{
	    //Checking if the banner got content from the impress call.
	    if(typeof banner == 'object' && banner.hasContent())
	    {
		//Prepare the banner html.
		iqBannerPrepare(banner);
		for(var z in banner.contents)
		{
		    if(typeof banner.contents[z] == 'object')
		    {
			//Add the content to the banner.
			iqBannerAddContent(banner, banner.contents[z]);
		    }
		}

		//Start the animation of the banner.
		iqBannerAnimate(banner, true);
	    }
	}
    }
    
    if(!iqnomy)
    {
	for(var i in iqProductSelections)
	{
	    var productSelection = iqProductSelections[i];
	    if(productSelection.enabled)
	    {
		productSelection.process();
	    }
	}
    }
    
    if(iqNewsletterObject != null && iqNewsletterObject.isIqnomyBanner == iqnomy && iqNewsletterObject.enabled)
    {
	iqNewsletterObject.process();
    }
}

function iqImpressBackupCallback()
{
    //Do nothing
}

function iqBannerPrepare(banner)
{
    if(banner.isIqnomyBanner)
    {
	if(typeof jQuery != "undefined")
	{
	    var caroussel = jQuery("#" + banner.htmlId).data("caroussel");
	    if(typeof caroussel != "undefined")
	    {
		caroussel.pause();
	    }
	    if(jQuery("#" + banner.htmlId).find(".iqnomy-prepared").length > 0)
	    {
		jQuery("#" + banner.htmlId).children().remove();
	    }
	}
    }
    else
    {
	var originalElement = IQJquery("#" + banner.htmlId);
        var bannerElement = originalElement.clone();
	
	bannerElement.attr("id", banner.htmlId + "-prepared");
        bannerElement.css("height", banner.height + "px");
        bannerElement.css("overflow", "hidden");
	
	if(bannerElement.css('position') != 'relative' && bannerElement.css('position') != 'absolute')
	{
	    bannerElement.css('position', 'relative');
	}
	
	bannerElement.removeClass("animated");
	bannerElement.children().remove();
	bannerElement.insertAfter(IQJquery("#" + banner.htmlId));
	IQJquery("#" + banner.htmlId).hide();
	banner.htmlId = banner.htmlId + "-prepared";

//        if(banner.animationType != 'none')
//        {
//            if(bannerElement.css("position") != "relative" && bannerElement.css("position") != "absolute")
//            {
//                bannerElement.css("position", "relative");
//            }
//
////            var initialContent = IQJquery("<div style='position: absolute; left: 0px; top: 0px; bottom: 0px;'></div>");
////            bannerElement.children().each(function()
////            {
////                initialContent.append(IQJquery(this).clone());
////                IQJquery(this).remove();
////            });
////            bannerElement.append(initialContent);
//        }
    }
}

function iqBannerAddContent(banner, content)
{    
    var bannerElement = IQJquery("#" + banner.htmlId);
    if(banner.isIqnomyBanner)
    {	
	if(typeof jQuery != "undefined")
	{
	    bannerElement.append(content.html);
	}
    }
    else if(banner.animationType != 'none')
    {
        if(banner.animationType == 'sliding')
            var html = IQJquery("<div class='iq-slide-active' style='position: absolute; left: 0%; top: 0px; bottom: 0px;'></div>");
        else
            var html = IQJquery("<div class='iq-slide-active' style='position: absolute; left: 0px; top: 0px; bottom: 0px;'></div>");
        
        var pause = IQJquery("<input type='hidden' class='pause-time' value='" + banner.pause + "'>");
    
	if(bannerElement.children().length > 0)
	{
	    html.removeClass("iq-slide-active");
	    if(banner.animationType == 'sliding')
	    {
	    	html.css("left", "100%");
	    }
	    else
	    {
		html.css("display", "none");
	    }
	}
    
        html.append(pause);
	html.addClass("iqnomy-content");
        html.append(IQJquery(content.html));
    }
    else
    {
        var html = IQJquery(content.html);
        html.hide();
        html.addClass("iq-content");
    }
    bannerElement.append(html);
    iqActivateChildren(html);
}

function iqActivateChildren(element)
{
    if(typeof element.children() != 'undefined' && element.children().length > 0)
    {
	element.children().each(function()
	{
	    if(IQJquery(this).hasClass('iq-active'))
	    {
		IQJquery(this).show();
	    }
	    iqActivateChildren(IQJquery(this));
	});
    }
}

function previewLiquidContent(containerId, template)
{
    if(iqContentToBanner(containerId, template, 0, "", true))
    {
	var banner = iqGetBanner(containerId);

	//Checking if the banner got content from the impress call.
	if(typeof banner == 'object' && banner != null && banner.hasContent())
	{
	    //Prepare the banner html.
	    iqBannerPrepare(banner);
	    for(var z in banner.contents)
	    {
		if(typeof banner.contents[z] == 'object')
		{
		    //Add the content to the banner.
		    iqBannerAddContent(banner, banner.contents[z]);
		}
	    }

	    //Start the animation of the banner.
	    iqBannerAnimate(banner, true);
	}
    }
    if(iqContentToProductSelection(containerId, template, 0, true))
    {
	var productSelection = iqGetProductSelection(containerId);
	if(typeof productSelection == 'object' && productSelection != null)
	{
	    productSelection.process();
	}
    }
    
    if(iqNewsletterObject != null && typeof iqNewsletterObject == 'object' && iqNewsletterObject.iqContainerId == containerId)
    {
	var impressionElement = {};
	impressionElement.htmlTemplate = template;
	
	var impression = {};
	impression.imprElements = [impressionElement];
	
	iqNewsletterObject.containerImpression = impression;
	iqNewsletterObject.process();
    }
}

function iqBannerAnimate(banner, removeCurrentSlide)
{
    if(!banner.isLoaded())
    {
        setTimeout(function()
        {
            iqBannerAnimate(banner, removeCurrentSlide);
        }, 200);
        return;
    }
    
    if(banner.isIqnomyBanner)
    {
	iqBannerAnimateFinal(banner, removeCurrentSlide);
    }
    else
    {
	var showTime = IQJquery("#" + banner.htmlId).children().first().find("input.pause-time").first().val();
	if(typeof showTime == 'undefined')
	    showTime = 100;
	else
	    showTime = parseInt(showTime);

	setTimeout(function()
	{
	    iqBannerAnimateFinal(banner, removeCurrentSlide);
	}, showTime);
    }
}

function iqBannerAnimateFinal(banner, removeCurrentSlide)
{
    removeCurrentSlide = false;
    if(banner.isIqnomyBanner)
    {
	if(typeof jQuery != "undefined")
	{
	    var caroussel = jQuery("#" + banner.htmlId).data("caroussel");
	    if(typeof caroussel != "undefined")
	    {
		caroussel.panels = caroussel.wrapper.find(caroussel.settings.panelSelector);
		caroussel.initAutoPlay();
	    }
	    //element.append(child);
	}
	
//        var element = IQJquery("#" + banner.htmlId);
//        element.animate({opacity: 0}, 100, function()
//        {
//            element.find("li").not(".iqnomy-prepared").remove();
//            element.find("li").hide();
//            element.find("li").first().show();
//            
//	    if(typeof jQuery != "undefined")
//	    {
//		var caroussel = jQuery("#" + banner.htmlId).data("caroussel");
//		if(typeof caroussel != "undefined")
//		{
////		    caroussel.initialize();
////		    caroussel.play();
//		}
//	    }
//	    element.animate({opacity: 1}, 100);
//        });
        
    }
    else if(banner.animationType != 'none')
    {
        var holder = IQJquery("#" + banner.htmlId);
	holder.removeClass("animated");
        if(holder.length && holder.children().length && holder.children().length > 0)
        {
            var currentSlide = holder.find(".iq-slide-active").first();
            if(!currentSlide.length)
            {
                var currentSlide = holder.find("div").first();
            }

            var nextSlide = currentSlide.next();
            if(!nextSlide.length)
                nextSlide = holder.find("div").first();

            if(currentSlide.length && nextSlide.length && currentSlide != nextSlide && (holder.children().length > 1 || !currentSlide.hasClass("iq-slide-active")))
            {
                var showTime = nextSlide.find("input.pause-time").first().val();
                if(typeof showTime == 'undefined')
                    showTime = 5000;
                else
                    showTime = parseInt(showTime);

                if(banner.animationType == 'sliding')
                {
		    if(holder.children().length > 1)
		    {
			currentSlide.removeClass("iq-slide-active");
			currentSlide.animate({ left: "-100%"}, {easing: "swing", duration: banner.duration, complete: function()
			{
			    if(currentSlide.hasClass("iqnomy-content"))
			    {
				currentSlide.css("left", "100%");
			    }
			    else
			    {
				currentSlide.remove();
			    }

			    setTimeout(function()
			    {
				iqBannerAnimateFinal(banner);
			    }, showTime);
			}});
		    }
		    else
		    {
			setTimeout(function()
			{
			    iqBannerAnimateFinal(banner);
			}, showTime);
		    }

                    nextSlide.addClass("iq-slide-active");
                    nextSlide.animate({ left: "0%"}, {easing: "swing", duration: banner.duration});
                }
                else
                {
		    if(holder.children().length > 1)
		    {
			currentSlide.removeClass("iq-slide-active");
			currentSlide.fadeOut(banner.duration, function()
			{
			    if(!currentSlide.hasClass("iqnomy-content"))
			    {
				currentSlide.remove();
			    }

			    setTimeout(function()
			    {
				iqBannerAnimateFinal(banner);
			    }, showTime);
			});
		    }
		    else
		    {
			setTimeout(function()
			{
			    iqBannerAnimateFinal(banner);
			}, showTime);
		    }
		    
                    nextSlide.addClass("iq-slide-active");
                    nextSlide.fadeIn(banner.duration);
                }
            }
        }
    }
    else
    {
        var bannerElement = IQJquery("#" + banner.htmlId);
        if(bannerElement.children().not(".iq-content").length)
        {
            bannerElement.children().not(".iq-content").fadeOut(100, function()
            {
                bannerElement.children().not(".iq-content").remove();
                bannerElement.find(".iq-content").fadeIn(100);
            });
        }
        else
        {
            bannerElement.find(".iq-content").fadeIn(100);
        }
    }
}

//
//window.ieVersion = function () 
//{ 
//    var regular; 
//    if (navigator.appName == "Microsoft Internet Explorer") 
//    { 
//	regular = new RegExp("MSIE ([0-9]+)");
//	return regular.exec(navigator.userAgent) ? RegExp.$1 : false; 
//    } 
//    else if (navigator.appName == "Netscape") 
//    { 
//	regular = new RegExp(".NET CLR ([0-9.]*); I63rv:([0-9]+)"); 
//	return regular.exec(navigator.userAgent) ? RegExp.$2 : false; 
//    } 
//    return false; 
//}; 
//window.lostfocus = function (event) 
//{ 
//    event = event ? event : window.event; 
//    var from = event.relatedTarget || event.toElement; 
//    if (!from || from.nodeName == "HTML") 
//    { 
//	var margin = 20; 
//	if (event.clientY <= 0 + margin && !iqIsShown) 
//	{ 
//	    iqIsShown = true; 
//	    IQImpressor.trackEvent(_iqsTenant, "WEBSHOP", {contentShowed_4333:"true"});
//	    document.getElementById("_iqContentBoxIQLeadgeneration").style.display = "block"; 
//	} 
//    } 
//}; 
//(function () 
//{ 
//    var _iqContentBoxIQLeadgeneration = document.getElementById("_iqContentBoxIQLeadgeneration"); 
//    if (typeof _iqContentBoxIQLeadgeneration === undefined || _iqContentBoxIQLeadgeneration.innerHTML.length === 0) 
//    { 
//	return false; 
//    } 
//    var newsletterSubscriberFormDetailIqnomy = new VarienForm("newsletter-validate-detail-iqnomy"); 
//    window.iqIsShown = false; 
//    var iqIeVersion = ieVersion(); 
//    if (iqIeVersion && iqIeVersion <= 8) 
//    { 
//	document.attachEvent("onmouseout", lostfocus); 
//    } 
//    else 
//    { 
//	document.addEventListener("mouseout", lostfocus, false); 
//    } 
//    var iqCloseImageBox = document.getElementById("iq-newpopup-close");
//    iqCloseImageBox.onclick = function() 
//    {  
//	IQImpressor.trackEvent(_iqsTenant, "WEBSHOP", {contentClosed_4333:"true"});
//	document.getElementById("_iqContentBoxIQLeadgeneration").style.display = "none"; 
//    }; 
//    var iqSubmitForm = document.getElementById("newsletter-validate-detail-iqnomy"); 
//    iqSubmitForm.onsubmit= function()
//    { 
//	if(newsletterSubscriberFormDetailIqnomy.validator.validate())
//	{
//	    IQImpressor.trackEvent(_iqsTenant, "WEBSHOP", {contentSentOrClicked_4333:"true"}); 
//	    var iqLqcid = document.getElementById("_iqLqcid").innerHTML; 
//	    IQImpressor.trackContainerClick(iqLqcid); 
//	    document.getElementById("_iqContentBoxIQLeadgeneration").style.display = "none"; 
//	}
//	return true; 
//    }; 
//})();