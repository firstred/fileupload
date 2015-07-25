<?php
/**
 * 2015 Michael Dekker
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@michaeldekker.com so we can send you a copy immediately.
 *
 * @author    Michael Dekker <prestashop@michaeldekker.com>
 * @copyright 2015 Michael Dekker
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
require_once(_PS_ADMIN_DIR_.'/../config/config.inc.php');
require_once(_PS_ADMIN_DIR_.'/init.php');

$name_explode = explode('.', Tools::getValue('img'));
if (Tools::isSubmit('img') && Validate::isMd5($name_explode[0]) && Tools::isSubmit('name') &&
    Validate::isGenericName(Tools::getValue('name')) && file_exists(_PS_UPLOAD_DIR_.Tools::getValue('img'))) {
    if (count($name_explode) >= 2) {
        $current_extension = Tools::strtolower($name_explode[count($name_explode) - 1]);
        header('Content-type: '.system_extension_mime_type(Tools::getValue('img')));
        header('Content-Disposition: attachment; filename="'.Tools::getValue('img').'"');
    } else {
        header('Content-type: image/jpeg');
        header('Content-Disposition: attachment; filename="'.Tools::getValue('name').'.jpg"');
    }
    echo Tools::file_get_contents(_PS_UPLOAD_DIR_.Tools::getValue('img'));
}

function system_extension_mime_types()
{
    # Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
    $out = array();
    $file = fopen('/etc/mime.types', 'r');
    while (($line = fgets($file)) !== false) {
        $line = trim(preg_replace('/#.*/', '', $line));
        if (!$line) {
            continue;
        }
        $parts = preg_split('/\s+/', $line);
        if (count($parts) == 1) {
            continue;
        }
        $type = array_shift($parts);
        foreach ($parts as $part) {
            $out[$part] = $type;
        }
    }
    fclose($file);

    return $out;
}

function system_extension_mime_type($file)
{
    # Returns the system MIME type (as defined in /etc/mime.types) for the filename specified.
    #
    # $file - the filename to examine
    static $types;
    if (!Tools::getIsset($types)) {
        $types = system_extension_mime_types();
    }
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (!$ext) {
        $ext = $file;
    }
    $ext = Tools::strtolower($ext);

    return isset($types[$ext]) ? $types[$ext] : null;
}

function system_mime_type_extensions()
{
    # Returns the system MIME type mapping of MIME types to extensions, as defined in /etc/mime.types (considering the first
    # extension listed to be canonical).
    $out = array();
    $file = fopen(_PS_MODULE_DIR_.'fileupload/mime.types', 'r');
    while (($line = fgets($file)) !== false) {
        $line = trim(preg_replace('/#.*/', '', $line));
        if (!$line) {
            continue;
        }
        $parts = preg_split('/\s+/', $line);
        if (count($parts) == 1) {
            continue;
        }
        $type = array_shift($parts);
        if (!isset($out[$type])) {
            $out[$type] = array_shift($parts);
        }
    }
    fclose($file);

    return $out;
}

function system_mime_type_extension($type)
{
    # Returns the canonical file extension for the MIME type specified, as defined in /etc/mime.types (considering the first
    # extension listed to be canonical).
    #
    # $type - the MIME type
    static $exts;
    if (!isset($exts)) {
        $exts = system_mime_type_extensions();
    }

    return isset($exts[$type]) ? $exts[$type] : null;
}
