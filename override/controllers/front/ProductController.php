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

class ProductController extends ProductControllerCore
{
    protected function pictureUpload()
    {
        if (!$field_ids = $this->product->getCustomizationFieldIds()) {
            return false;
        }
        $authorized_file_fields = array();
        foreach ($field_ids as $field_id) {
            if ($field_id['type'] == Product::CUSTOMIZE_FILE) {
                $authorized_file_fields[(int)$field_id['id_customization_field']] =
                    'file'.(int)$field_id['id_customization_field'];
            }
        }
        $indexes = array_flip($authorized_file_fields);
        foreach ($_FILES as $field_name => $file) {
            if (in_array($field_name, $authorized_file_fields) && isset($file['tmp_name']) && !empty($file['tmp_name'])
            ) {
                $current_extension = '.jpg';
                $name_explode = explode('.', $file['name']);
                if (count($name_explode) >= 2) {
                    $current_extension = Tools::strtolower($name_explode[count($name_explode) - 1]);
                }
                $file_name = md5(uniqid(rand(), true)).'.'.$current_extension;
                if ($error = $this->validateUploadReplacement(
                    $file,
                    (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE')
                )
                ) {
                    $this->errors[] = $error;
                }

                $product_picture_width = (int)Configuration::get('PS_PRODUCT_PICTURE_WIDTH');
                $product_picture_height = (int)Configuration::get('PS_PRODUCT_PICTURE_HEIGHT');
                $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if ($error || (!$tmp_name || !move_uploaded_file($file['tmp_name'], _PS_UPLOAD_DIR_.$file_name))) {
                    return false;
                }
                /* A smaller one */
                if (ImageManager::isRealImage(_PS_UPLOAD_DIR_.$file_name, $file['type'])) {
                    if (!ImageManager::resize(
                        _PS_UPLOAD_DIR_.$file_name,
                        _PS_UPLOAD_DIR_.$file_name.'_small',
                        $product_picture_width,
                        $product_picture_height
                    )) {
                        $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                    }
                } else {
                    $icon = _PS_MODULE_DIR_.'fileupload/views/img/icons/_blank.png';
                    if (file_exists(_PS_MODULE_DIR_.'fileupload/views/img/icons/'.$current_extension.'.png')) {
                        $icon = _PS_MODULE_DIR_.'fileupload/views/img/icons/'.$current_extension.'.png';
                    }

                    if (!ImageManager::resize(
                        $icon,
                        _PS_UPLOAD_DIR_.$file_name.'_small',
                        $product_picture_width,
                        $product_picture_height
                    )) {
                        $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                    }
                }
                if (!chmod(_PS_UPLOAD_DIR_.$file_name, 0777)
                ) {
                    $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                } else {
                    $this->context->cart->addPictureToProduct(
                        $this->product->id,
                        $indexes[$field_name],
                        Product::CUSTOMIZE_FILE,
                        $file_name
                    );
                }
                unlink($tmp_name);
            }
        }
        return true;
    }

    /**
     * Validate file upload (check image type and weight)
     * Replaces ImageManager::validateUpload()
     *
     * @param array $file Upload $_FILE value
     * @param int $max_file_size Maximum upload size
     * @param object $types File type perhaps?
     * @return bool|string Return false if no error encountered
     */
    private function validateUploadReplacement($file, $max_file_size = 0, $types = null)
    {
        if ((int)$max_file_size > 0 && $file['size'] > (int)$max_file_size) {
            return sprintf(
                Tools::displayError('File is too large (%1$d kB). Maximum allowed: %2$d kB'),
                $file['size'] / 1024,
                $max_file_size / 1024
            );
        }
        if (!$this->isCorrectFileExtReplacement($file['name'], $types)) {
            return Tools::displayError('File format not allowed');
        }
        if ($file['error']) {
            return sprintf(
                Tools::displayError(
                    'Error while uploading image; please change your server\'s settings. (Error code: %s)'
                ),
                $file['error']
            );
        }
        return false;
    }

    /**
     * Check if file extension is correct
     *
     * @param string $filename Real filename
     * @return bool True if it's correct
     */
    private function isCorrectFileExtReplacement($filename)
    {
        // Filter on file extension
        // Get file extension from database
        $authorized_extensions = unserialize(Configuration::get('FILEUPLOAD_FILE_EXTS'));
        if (count($authorized_extensions) === 1 && $authorized_extensions[0] === '') {
            return true;
        }

        $name_explode = explode('.', $filename);
        if (count($name_explode) >= 2) {
            $current_extension = Tools::strtolower($name_explode[count($name_explode) - 1]);
            if (!in_array($current_extension, $authorized_extensions)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
}
