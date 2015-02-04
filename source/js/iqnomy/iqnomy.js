/**
 * IQNOMY JavaScript helper.
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

var _iqsHelper = (function(global) {
    var self = {},
        timer = false,
        retries = 0,
        eventQueue = [],
        config = {
            logging: false,
            ignoreDnt: false
        };

    /**
     * Initialize IQNOMY helper.
     *
     * @param initConfig
     */
    self.init = function(initConfig) {
        config = Object.extend(config, initConfig);
        // IQNOMY: removed Do-Not-Track check at the request of IQNOMY
        /*if (!config.ignoreDnt && (navigator.doNotTrack || navigator.msDoNotTrack)) {
            if (config.logging && typeof console.log == 'function') {
                console.log('Respecting Do-Not-Track');
            }
            return false;
        }*/
        if (config.logging && typeof console.log == 'function') {
            console.log('IQNOMY helper initialized');
        }
        return true;
    };

    /**
     * Merge events from the queue into one object and clear the queue.
     */
    var getMergedEvent = function() {
        var i, key, eventData = {}, isEmpty = true;
        for (i = 0; i < eventQueue.length; i++) {
            for (key in eventQueue[i]) {
                if (eventQueue[i].hasOwnProperty(key)) {
                    eventData[key] = eventQueue[i][key];
                    isEmpty = false;
                }
            }
        }
        eventQueue = [];
        return isEmpty ? false : eventData;
    };

    /**
     * Send all events in the queue to IQNOMY. If the IQImpressor object is not available,
     * set a timer and try again after one second.
     *
     * @global _iqsTenant
     */
    var processQueue = function() {
        if (timer) {
            clearTimeout(timer);
            timer = false;
        }
        if (typeof IQImpressor != 'undefined' && typeof _iqsTenant != 'undefined') {
            retries = 0;
            IQImpressor.trackEvent(_iqsTenant, 'WEBSHOP', getMergedEvent());
        }
        else if (eventQueue.length > 0 && retries < 5) {
            retries++;
            if (config.logging && typeof console.log == 'function') {
                console.log('IQImpressor not loaded (' + retries + ' retries');
            }
            timer = setTimeout(processQueue, 1000);
        }
    };

    /**
     * Attach event handlers to corresponding DOM nodes.
     */
    Event.observe(document, 'dom:loaded', function() {
        $$('ul.nav-tabs a').each(function (el) {
            if (el.href.match(/#.*attributes/i)) {
                el.observe('click', function () {
                    self.trackEvent({'details':'attributes'});
                });
            }
            else if (el.href.match(/#.*reviews/i)) {
                el.observe('click', function () {
                    self.trackEvent({'details':'reviews'});
                });
            }
        });
    });

    /**
     * Track IQNOMY event. Works during or after page load, as this file is included in the
     * html head.
     *
     * @param eventData
     */
    self.trackEvent = function(eventData) {
        if (config.logging && typeof console.log == 'function') {
            console.log('Track IQNOMY event ', eventData);
        }
        eventQueue.push(eventData);
        processQueue();
    };

    /**
     * Retrieve queued events (during page load) for use as _iqsEventData parameter for the
     * IQImpressor script.
     *
     * @param initialEventData
     */
    self.getEventData = function(initialEventData) {
        if (timer) {
            clearTimeout(timer);
            timer = false;
        }
        if (initialEventData) {
            eventQueue.unshift(initialEventData);
        }
        return getMergedEvent();
    };

    return self;
}(this));
