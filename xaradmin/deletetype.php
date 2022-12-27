<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Delete an tag type
 *
 * @public
 * @author Richard Cave
 * @return array, or false on failure
 * @throws BAD_PARAM
 */
function html_admin_deletetype()
{
    // Security Check
    if (!xarSecurity::check('ManageHTML')) {
        return;
    }

    // Get parameters from input
    if (!xarVar::fetch('id', 'int:0:', $id)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'int:0:1', $confirm, 0)) {
        return;
    }

    // Get the current html tag
    $type = xarMod::apiFunc(
        'html',
        'user',
        'gettype',
        ['id' => $id]
    );

    // Check for exceptions
    if (!isset($type) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return;
    } // throw back

    // Check for confirmation.
    if (!$confirm) {
        // Specify for which html tag you want confirmation
        $data['id'] = $id;

        // Data to display in the template
        $data['type'] = xarVar::prepForDisplay($type['type']);
        $data['submitlabel'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSec::genAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        $msg = xarML(
            'Invalid authorization key for deleting #(1) HTML tag #(2)',
            'HTML',
            xarVar::prepForDisplay($id)
        );
        return xarResponse::notFound();
    }

    // Remove the html tag
    if (!xarMod::apiFunc(
        'html',
        'admin',
        'deletetype',
        ['id' => $id]
    )) {
        return; // throw back
    }

    xarSession::setVar('statusmsg', xarML('HTML Tag Deleted'));

    // Redirect
    xarController::redirect(xarController::URL('html', 'admin', 'viewtypes'));

    // Return
    return true;
}
