/**
 * This file is loaded by the abstract SystemComponentBackendController
 * You may add own JS files using
 * \JS::registerJS(substr($this->getDirectory(false, true) . '/View/Script/FileName.css', 1));
 * or remove this file if you don't need it
 */

jQuery(document).ready(function($){
    if ( $( "#form-0-emailAccess" ).length ) {
        var $value = $( "#form-0-emailAccess" ).val();

        $( '[name="emailAccess"]' ).val($value);
    }

    if ( $( "#form-0-backendLangId" ).length ) {
        var $value = $( "#form-0-backendLangId" ).val();

        $( '[name="backendLangId"]' ).val($value);
    }
});
