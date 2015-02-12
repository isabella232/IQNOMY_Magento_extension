###############
Getting Started
###############
.. _magentogettingstarted:

Installing
==========

First make sure that you have created an IQNOMY account and can login to IQNOMY to get the needed information for the extension configuration. You haven't done that yet: `create your account`_
If you are going to install the IQNOMY Magento extension, please know what you are doing. Or let it be done by one of our partners_.


Download
========

Of course you need the extension: download_ here

Module installation
===================

* Click in the Magento backend on Systeem > Magento Connect > Magento Connect Manager

.. image:: ./_static/images/SettingsInstallMagentoA.png
.. image:: ./_static/images/SettingsInstallMagentoB.png

* Click in the 'Direct package file upload' on the button 'Bladeren'
* Search and select the file IQNOMY-extension_latest.tgz
* Click on the 'Upload'. The module is going to be installed.
* Click on 'Return to Admin' to go back to the Magento backend

.. image:: ./_static/images/SettingsInstallMagento1.png
.. image:: ./_static/images/SettingsInstallMagento2.png
.. image:: ./_static/images/SettingsInstallMagento3.png
.. image:: ./_static/images/SettingsInstallMagento4.png
.. image:: ./_static/images/SettingsInstallMagento5.png

Module configuration
====================
You can find the configuration in the Magento backend in System > Configuration > Services > IQNOMY Connector

Fill in your IQNOMY account information

* Username: the emailadres you use to login to IQNOMY.
* Account: User the IQNOMY id of your environment Example 763096175
* Webservice key: API key

You can find your IQNOMY ID and Webservice key in your IQNOMY account in Discovery > Connect IQNOMY

.. image:: ./_static/images/SettingsInstallMagento4.png
.. image:: ./_static/images/SettingsInstallMagento6.png
.. image:: ./_static/images/SettingsInstallMagento7.png
.. image:: ./_static/images/SettingsInstallMagento8.png

You can set the usable [[Dimensions|dimensions]]. Standard configuration are all product attributes where 'Visible in frontend'  or 'Used in comparison' is set.

These dimensions will be send to IQNOMY when a visitor is visiting a for example a product page. Also all options from select and multiselect attributes will be send to IQNOMY when the attribute is saved in the backend. 

After you have changed the dimension that have to be used you have to synchronize again. This is not an automatic process.

.. image:: ./_static/images/SettingsInstallMagento9.png

Synchronisation dimensions
==========================

You can send the dimensions (attributes) and the properties (attribute-options) from Magento to IQNOMY in two ways:

#. One attribute: If you edit an attribute through Catalog > Attributes > Manage attributes. The attribute will be send automatically when saving.
#. All attributes at once: Go to Customers > IQNOMY Connector and click on the synchronize button.

The properties (attribute properties) will be send as a key/value to IQNOMY. So if you change the description (label) it will still be recognized. 

.. image:: ./_static/images/SettingsInstallMagento11.png
.. image:: ./_static/images/SettingsInstallMagento12.png
.. image:: ./_static/images/SettingsInstallMagento10.png
.. image:: ./_static/images/SettingsInstallMagento13.png
.. image:: ./_static/images/SettingsInstallMagento14.png

Tracking
========

Every page in the frontend will enclose the standard IQNOMY script just before the closing </body> tag. This standard IQNOMY script will track all the normal pageviews through a Javascript. Next to these page views the following events will be tracked. 

Through a REST api (iqeventdata value is between '( )' ): 

* If a visitor registers a new account (account=register)
* If a visitor logs in (account=login)
* If a visitor subscribes for a newsletter (newsletter=true)
* If a visitor posts a contact form (contactform=true)
* If a visitor changes the content of a shopping cart (cart_changed=true, subtotal=<bedrag>, orderrows=[{product_id:<id>,quantity:<aantal>,price:<bedrag>}, ...])
* If a visitor does a checkout of the order (checkout=true)

Through JavaScript (during page-load, _iqsEventData value between '( )'  ):

* If a visitor visits the homepage (page_type=home)
* If a visitor visits a CMS page  (page_type=info)
* If a visitor visits a category page (page_type=overview, category_id=<id>)
* If a visitor visits a product detail page (page_type=detail, product_id=<id>, category_id=<id>, <dimension>=<value>, ...)
* If a visitor visits the shopping cart  (page_type=shoppingcart)
* If a visitor visits the order page  (page_type=checkout)
* If a visitor visits the search result page (page_type=search, search=<zoekterm>)
* If a visitor visits the wish list (page_type=wishlist, products=[{product_id:<id>,category_id:<id>,<dimension>:<value>,...}])
* If a visitor visits the product comparison  (page_type=compare, products=[{product_id:<id>,category_id:<id>,<dimension>:<value>,...}])

Through JavaScript (after visitor action, through trackEvent functie):

* If a visitor on product detail page clicks the tab Product properties (details=attributes)
* If a visitor on product detail page clicks the tab Reviews (details=reviews)
* If the filters on a category page are used by a visitor  (filter=true, <dimension>=<value>, …)
* If the sorting on the category page is used  (order=<dimension>, direction=asc/desc)

.. image:: ./_static/images/SettingsInstallMagento15.png
.. image:: ./_static/images/SettingsInstallMagento16.png

.. _create your account: http://www.iqnomy.com/nl/tarieven
.. _partners: http://campus.iqnomy.com/partners 
.. _download: http://www.iqnomy.com/downloads/magento/IQNOMY_Magento_extensie-latest.tgz

