<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class CoreLanguage
 *
 * @package ZCMS\Core\Models
 */
class CoreLanguages extends ZModel
{

    /**
     *
     * @var integer
     */
    public $language_id;

    /**
     *
     * @var string
     */
    public $language_code;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $icon;

    /**
     *
     * @var integer
     */
    public $is_default;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $metaKey;

    /**
     *
     * @var string
     */
    public $metaDesc;

    /**
     *
     * @var string
     */
    public $siteName;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     *
     * @var string
     */
    public $direction;

    public function initialize()
    {

    }

    /**
     * Set default language
     *
     * @param $language_code
     * @return bool
     */
    public function setDefaultLanguage($language_code)
    {
        /**
         * @var CoreLanguages $coreLanguage
         */
        $coreLanguage = CoreLanguages::findFirst([
            'conditions' => 'language_code = ?0',
            'bind' => [$language_code]
        ]);

        if ($coreLanguage) {
            $db = $this->_getDb();
            $query = "UPDATE core_languages SET is_default = 0";
            if ($db->execute($query)) {
                $coreLanguage->is_default = 1;
                return $coreLanguage->save();
            }
        }
        return false;
    }
}
