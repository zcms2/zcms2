<?php

namespace ZCMS\Core\Models;

use Phalcon\Mvc\Model;

/**
 * Class CoreTemplate
 *
 * @package ZCMS\Core\Models
 */
class CoreTemplates extends Model
{

    /**
     *
     * @var integer
     */
    public $template_id;

    /**
     *
     * @var string
     */
    public $base_name;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $location;

    /**
     *
     * @var string
     */
    public $uri;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $authorUri;

    /**
     *
     * @var string
     */
    public $tag;

    /**
     *
     * @var string
     */
    public $version;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    /**
     * Set default template
     *
     * @param $base_name
     * @param $location
     * @return bool
     */
    public function setDefaultTemplate($base_name, $location)
    {
        if (!($location == 'frontend' || $location == 'backend')) {
            $this->getDI()->get('flashSession')->error('gb_location_template_must_be_backend_or_frontend');
            return false;
        }

        /**
         * @var CoreTemplates $defaultTemplate
         */
        $defaultTemplate = CoreTemplates::findFirst([
            'conditions' => "location = ?0 AND base_name = ?1",
            'bind' => [$location, $base_name]
        ]);

        if ($defaultTemplate) {
            $phql = "UPDATE ZCMS\Core\Models\CoreTemplates SET published = 0 WHERE location = '{$location}'";
            if ($this->getDI()->get('modelsManager')->createQuery($phql)->execute()) {
                $defaultTemplate->published = 1;
                if ($defaultTemplate->save()) {
                    file_put_contents(APP_DIR . "/" . $location . "/index.volt", '{% extends "../../../templates/' . $location . "/" . $base_name . '/index.volt" %}');
                    return true;
                }
            }
        }
        return false;
    }
}
