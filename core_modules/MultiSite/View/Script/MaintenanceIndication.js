/**
 * MultiSite
 * @author: Thomas Däppen <thomas.daeppen@comvation.com>
 * @version: 1.0
 * @package: contrexx
 * @subpackage: coremodules_multisite
 */

/**
 * Position the maintenance-indication-bar
 */
cx.ready(function() {
    // fetch the current vertical position of the body
    var toolbarOffset = parseInt(cx.jQuery("body").css("padding-top"));
    if (!toolbarOffset) {
        toolbarOffset = 0;
    }

    // position the body and the maintenance-indication-bar
    cx.jQuery("body").css("padding-top", (parseInt(cx.jQuery("#MultiSiteMaintenanceIndication").outerHeight()) + toolbarOffset) + "px");
    cx.jQuery("#MultiSiteMaintenanceIndication").css({
        top: toolbarOffset + "px"
    });
});
