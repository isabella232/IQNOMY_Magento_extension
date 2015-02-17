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

You can set the usable dimensions. Standard configuration are all product attributes where 'Visible in frontend'  or 'Used in comparison' is set.

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

To see if the visitor tracking is working you can login your IQNOMY account and check the livestream. The livestream will show you the data of visitors that comes in.

.. image:: ./_static/images/SettingsInstallMagento15.png
.. image:: ./_static/images/SettingsInstallMagento16.png

The tracking is sending data about an event (pagevisit, search, ordering, etc) to the IQNOMY platform. The following events are being captured:
In the livestream above you can see that every event contains data. With the extension we have defined what kind of data IQNOMY will receive from the extension.

Below you can see the possible event values. Custom event values can also be created by website developer.

Magento event values
--------------------

* account	=register
* account	=login
* newsletter	=true
* contactform	=true
* cart_changed	=true
* subtotal	={amount}
* checkout	=true
* page_type	=home
* page_type	=info
* page_type	=overview
* page_type	=detail
* page_type	=shoppingcart
* page_type	=checkout
* page_type	=search
* page_type	=wishlist
* page_type	=compare
* details	=attributes
* details	=reviews
* filter	=true
* search	={*text* visitor types}
* {product attributes} = {value}

.. note::
   Examples product attributes:

   * color = green
   * size = L
   * available = true

* orderrows	={{order},{order}}
* category_id	={category_id}
* product_id	={product_id}
* quantity	={quantity}
* price	={amount}

.. note::
   All the values between {} are variables. Depending on the visitor, product or page this value will change

Building a profile
==================
Based on all the events we receive from a visitor IQNOMY will automaticly create a profile. The event data is being used to create a higher level profile. We call this *Dimensions*.


.. _create your account: http://www.iqnomy.com/nl/tarieven
.. _partners: http://campus.iqnomy.com/partners 
.. _download: http://www.iqnomy.com/downloads/magento/IQNOMY_Magento_extensie-latest.tgz

