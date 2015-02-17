##########
Developers
##########

The extension has multiple connections with the IQNOMY platform through REST en browser javascript. The **main functionalities** of the extension are:

#. Managing your IQNOMY account
#. Tracking and webshop visitors

.. contents::

*************************
Management IQNOMY account
*************************

From the extension you can configure part of your IQNOMY account.

.. seealso::
   :doc:`administrators`

Connection info
===============
The API information can be found in the IQNOMY platform. You need this to connect the extension with the IQNOMY platform.


****************
Webshop visitors
****************

The IQNOMY script is automaticly installed with the extension.

Tracking data
=============

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

Personalization
===============
With the management part of the extension the personalization can be configured. An administrator can also configure the personalisation directly in the IQNOMY platform.

With every track that is performed the IQNOMY platform will check if the URL the visitor is on also needs personalization. If this is the case IQNOMY will inject the HTML in the Magento webshop.

*******************
Extension structure
*******************

61 directories, 82 files

.. code-block:: text

   ├── app
   │   ├── code
   │   │   └── community
   │   │       └── IQNOMY
   │   │           └── Extension
   │   │               ├── Block
   │   │               │   ├── Adminhtml
   │   │               │   │   ├── Banner
   │   │               │   │   │   ├── Edit
   │   │               │   │   │   │   └── Form.php
   │   │               │   │   │   ├── Edit.php
   │   │               │   │   │   └── Grid.php
   │   │               │   │   ├── Banner.php
   │   │               │   │   ├── Case.php
   │   │               │   │   ├── Container
   │   │               │   │   │   ├── Edit
   │   │               │   │   │   │   ├── Containerpages.php
   │   │               │   │   │   │   └── Form.php
   │   │               │   │   │   ├── Edit.php
   │   │               │   │   │   └── Grid.php
   │   │               │   │   ├── Container.php
   │   │               │   │   ├── Control.php
   │   │               │   │   ├── Dashboard
   │   │               │   │   │   ├── Iqnomy.php
   │   │               │   │   │   └── Sales.php
   │   │               │   │   ├── Liquidcontent
   │   │               │   │   │   ├── Chooser.php
   │   │               │   │   │   ├── Edit
   │   │               │   │   │   │   ├── Advanced.php
   │   │               │   │   │   │   ├── Form.php
   │   │               │   │   │   │   ├── Product.php
   │   │               │   │   │   │   ├── Renderer
   │   │               │   │   │   │   │   └── Button.php
   │   │               │   │   │   │   └── Slider.php
   │   │               │   │   │   ├── Edit.php
   │   │               │   │   │   ├── Grid.php
   │   │               │   │   │   └── Image
   │   │               │   │   │       └── Chooser.php
   │   │               │   │   ├── Liquidcontent.php
   │   │               │   │   ├── Subscription
   │   │               │   │   │   └── Grid.php
   │   │               │   │   ├── Subscription.php
   │   │               │   │   └── System
   │   │               │   │       └── Config
   │   │               │   │           └── Form
   │   │               │   │               ├── Field
   │   │               │   │               │   ├── Dimensions
   │   │               │   │               │   │   └── Product.php
   │   │               │   │               │   └── Dimensions.php
   │   │               │   │               └── Fieldset
   │   │               │   │                   └── Versioninfo.php
   │   │               │   ├── Banner.php
   │   │               │   ├── Html.php
   │   │               │   └── Tracker.php
   │   │               ├── controllers
   │   │               │   ├── Adminhtml
   │   │               │   │   ├── BannerController.php
   │   │               │   │   ├── CaseController.php
   │   │               │   │   ├── ContainerController.php
   │   │               │   │   ├── IqnomyController.php
   │   │               │   │   ├── LiquidcontentController.php
   │   │               │   │   └── SubscriptionController.php
   │   │               │   └── ProductController.php
   │   │               ├── etc
   │   │               │   ├── adminhtml.xml
   │   │               │   ├── config.xml
   │   │               │   └── system.xml
   │   │               ├── Helper
   │   │               │   └── Data.php
   │   │               ├── Model
   │   │               │   ├── Adminhtml
   │   │               │   │   └── System
   │   │               │   │       └── Config
   │   │               │   │           └── Source
   │   │               │   │               ├── Category
   │   │               │   │               │   └── Level.php
   │   │               │   │               ├── Dimensions
   │   │               │   │               │   └── Product.php
   │   │               │   │               ├── Dimensions.php
   │   │               │   │               └── Environment.php
   │   │               │   ├── Banner.php
   │   │               │   ├── Case.php
   │   │               │   ├── Container.php
   │   │               │   ├── IQNOMYbanner.php
   │   │               │   ├── Liquidcontent.php
   │   │               │   ├── Product.php
   │   │               │   ├── Resource
   │   │               │   │   ├── Banner
   │   │               │   │   │   └── Collection.php
   │   │               │   │   ├── Banner.php
   │   │               │   │   ├── Mysql4
   │   │               │   │   │   └── Setup.php
   │   │               │   │   ├── Subscription
   │   │               │   │   │   └── Collection.php
   │   │               │   │   ├── Subscription.php
   │   │               │   │   ├── Template
   │   │               │   │   │   └── Collection.php
   │   │               │   │   └── Template.php
   │   │               │   ├── Subscription.php
   │   │               │   ├── Sync
   │   │               │   │   └── Observer.php
   │   │               │   ├── Template.php
   │   │               │   ├── Tracker.php
   │   │               │   └── Webservice.php
   │   │               └── sql
   │   │                   └── IQNOMY_setup
   │   │                       └── install-0.8.12.php
   │   ├── design
   │   │   └── adminhtml
   │   │       └── base
   │   │           └── default
   │   │               ├── layout
   │   │               │   └── iqnomy_extension.xml
   │   │               └── template
   │   │                   └── iqnomy_extension
   │   │                       ├── banner
   │   │                       │   └── grid.phtml
   │   │                       ├── case
   │   │                       │   └── overview.phtml
   │   │                       ├── container
   │   │                       │   ├── grid.phtml
   │   │                       │   └── pages.phtml
   │   │                       ├── control.phtml
   │   │                       ├── dashboard.phtml
   │   │                       ├── liquidcontent
   │   │                       │   └── grid.phtml
   │   │                       ├── store
   │   │                       │   └── switcher.phtml
   │   │                       └── subscription
   │   │                           └── grid.phtml
   │   ├── etc
   │   │   └── modules
   │   │       └── IQNOMY_Extension.xml
   │   └── locale
   │       └── nl_NL
   │           └── IQNOMY_Extension.csv
   ├── js
   │   └── iqnomy
   │       ├── backend.js
   │       ├── IQBanner.js
   │       ├── IQJquery.js
   │       └── iqnomy.js
   └── package.xml


***************
Troubleshooting
***************

Logging
=======

If there are any problems you can set with in the connector configuration 'Enable Logging' to 'Yes'. All API-calls will be logged in the var/log/iqnomy.log. This can be handy for a developer in case of troubleshooting

FTP configuration
=================

If this is your first Magento plugin, maybe you have to set the ftp upload to install the plugin.

.. image:: _static/images/SettingsFTPMagento1.png
.. image:: _static/images/SettingsFTPMagento2.png
.. image:: _static/images/SettingsFTPMagento3.png
.. image:: _static/images/SettingsFTPMagento4.png
.. image:: _static/images/SettingsFTPMagento5.png
.. image:: _static/images/SettingsFTPMagento6.png

Error after installing
======================

Just logout and login again in your Magento administration.

.. image:: _static/images/SettingsErrorMagento1.png
.. image:: _static/images/SettingsErrorMagento2.png

Maintenance mode
================

This has happened when installing the plugin twice or the installation process was interrupted.
* Unflag the maintenance mode
* Clear var/cache
