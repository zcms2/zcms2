<?php

namespace ZCMS\Core\Forms;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Forms\ElementInterface as FElementInterface;

/**
 * Class ZForm
 *
 * @package ZCMS\Core\Forms
 * @property \Phalcon\Security security
 */
class ZForm extends Form
{
    /**
     * Title column for SEO
     *
     * @var string
     */
    public $_titleColumn = '';

    /**
     * @var string
     */
    public $_formName = '';

    /**
     * @var bool
     */
    public $_autoGenerateTranslateLabel = true;

    /**
     * @var bool
     */
    public $_autoGenerateTranslateHelpLabel = false;

    /**
     * @var bool
     */
    public $bootstrap = true;

    /**
     * Set use bootstrap
     *
     * @param bool $bool
     */
    public function setBootstrap($bool = true)
    {
        $this->bootstrap = $bool;
    }

    /**
     * Check isValid
     *
     * @param array $data
     * @param object $entity
     * @param bool $setAttributeErrorName
     * @return bool
     */
    public function isValid($data = null, $entity = null, $setAttributeErrorName = true)
    {
        if ($this->_titleColumn != '') {
            $data = $this->repaidSEOData($data);
        }

        //Supper isValid on parent
        $return = parent::isValid($data, $entity);
        $elements = $this->getElements();
        if (count($elements)) {
            foreach ($elements as $element) {
                $class = $element->getAttribute("class");
                $element->setAttribute("class", remove_multi_space($class . " has-success"));
            }
        }

        //Get message error
        $messages = $this->getMessages();

        if (!$return) {
            foreach ($messages as $message) {
                if (method_exists($message, "getField")) {
                    $error_element = $this->get($message->getField());
                    $validator = $error_element->getValidators();
                    if (is_array($validator) && isset($validator[0]) && method_exists($validator[0], 'getOption')) {

                        //Get class error
                        $class_error = $validator[0]->getOption('class_error');
                        if (!$class_error) {
                            $class_error = "has-error";
                        }
                        //Get current class name in field
                        $currentClass = str_replace("has-success", "", $error_element->getAttribute('class'));
                        //Add new class name error in field
                        $error_element->setAttribute('class', remove_multi_space($currentClass . " " . $class_error));

                        if ($setAttributeErrorName) {
                            //Get attribute name
                            $attribute_error_name = $validator[0]->getOption('attribute_error_name');
                            if (!$attribute_error_name) {
                                $attribute_error_name = "data-content";
                            }

                            //Get attribute content
                            $attribute_error_content = $validator[0]->getOption('attribute_error_content');
                            if ($attribute_error_content) {
                                $attribute_error_content = __($attribute_error_content);
                            } else {
                                $message = $validator[0]->getOption('message');
                                if ($message) {
                                    $attribute_error_content = __($message);
                                } else {
                                    $attribute_error_content = __("gb_form_this_field_is_required");
                                }
                            }
                            //Add error data content in field
                            $error_element->setAttribute($attribute_error_name, $attribute_error_content);
                        }

                        //Re add element error
                        $this->add($error_element);
                    }
                }
            }
        }

        //Return supper isValid
        return $return;
    }

    /**
     * Build SEOForm
     *
     * @param mixed $data
     * @return $this
     */
    protected function buildSEOForm($data = null)
    {
        if ($data != null) {
            $metadataArray = json_decode($data->metadata, true);
            $robots = explode(',', $metadataArray['robots']);
            $data->zcms_seo_title = $metadataArray['title'];
            $data->redirect_301 = $metadataArray['redirect_301'];
            $data->zcms_meta_robot_index = isset($robots[0]) ? $robots[0] : null;
            $data->zcms_meta_robot_follow = isset($robots[1]) ? $robots[1] : null;
        }

        //Title
        $seoTitle = new Text('zcms_seo_title');
        $seoTitle->addValidator(new StringLength([
            'min' => 0,
            'max' => 255
        ]));
        $this->add($seoTitle);

        //Meta description
        $metaDesc = new TextArea('meta_desc', ['rows' => 4]);
        $metaDesc->addValidator(new StringLength(
            [
                'min' => 0,
                'max' => 255
            ]
        ));
        $this->add($metaDesc);

        //Meta keywords
        $metaKey = new Text('meta_keywords');
        $metaKey->addValidator(new StringLength([
            'min' => 0,
            'max' => 255
        ]));
        $this->add($metaKey);

        //Meta Robots Index:
        $metaRobotIndex = new Select('zcms_meta_robot_index', [
            'index' => 'Index',
            'noindex' => 'NoIndex',
        ]);
        $metaRobotIndex->addValidator(new InclusionIn([
            'domain' => ['index', 'noindex']
        ]));
        $this->add($metaRobotIndex);

        //Meta Robots Follow
        $metaRobotFollow = new Select('zcms_meta_robot_follow', [
            'follow' => 'Follow',
            'nofollow' => 'NoFollow',
        ]);
        $metaRobotFollow->addValidator(new InclusionIn([
            'domain' => ['follow', 'nofollow']
        ]));
        $this->add($metaRobotFollow);

        //Meta robot advance
        $metaRobotAdvance = new Select('zcms_meta_robot_advance',
            [
                'none' => 'None',
                'noodp' => 'NO ODP',
                'noydir' => 'NO YDIR',
                'noimageindex' => 'No Image Index',
                'noarchive' => 'No Archive',
                'nosnippet' => 'No Snippet',
            ],
            [
                'multiple' => 'multiple',
                'name' => 'zcms_meta_robot_advance[]'
            ]
        );
        $metaRobotAdvance->addValidator(new InclusionIn([
            'domain' => ['', 'none', 'noodp', 'noydir', 'noimageindex', 'noarchive', 'nosnippet']
        ]));
        $this->add($metaRobotAdvance);

        //Redirect 301
        $redirect301 = new Text('zcms_redirect_301');
        $this->add($redirect301);

        //Add metadata
        $metadata = new TextArea('metadata');
        $this->add($metadata);

        return $this;
    }

    /**
     * Repaid seo data
     *
     * @param $data
     * @return array
     */
    private function repaidSEOData($data)
    {
        if (is_array($data)) {
            if ($data['zcms_seo_title'] == '') {
                $data['zcms_seo_title'] = $data[$this->_titleColumn];
            }
            $data['zcms_metadata']['title'] = $data['zcms_seo_title'];
            $data['zcms_metadata']['robots'] = $data['zcms_meta_robot_index'] . ',' . $data['zcms_meta_robot_follow'];
            if (!empty($data['zcms_meta_robot_advance']) && !in_array('none', $data['zcms_meta_robot_advance'])) {
                $data['zcms_metadata']['robots'] = implode(',', $data['zcms_meta_robot_advance']);
            } else {
                $data['zcms_metadata']['robots'] = $data['zcms_meta_robot_index'] . ',' . $data['zcms_meta_robot_follow'];
            }
            $data['zcms_metadata']['redirect_301'] = $data['zcms_redirect_301'];
            $data['zcms_metadata']['description'] = $data['meta_desc'];
            $data['zcms_metadata']['keywords'] = $data['meta_keywords'];
            $data['metadata'] = json_encode($data['zcms_metadata']);
        }
        return $data;
    }

    /**
     * Generate the label of a element added to the form including HTML
     *
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function label($name, array $attributes = NULL)
    {
        if ($this->_autoGenerateTranslateHelpLabel && $this->_formName) {
            if (!isset($attributes['data-toggle'])) {
                $attributes['data-toggle'] = 'tooltip';
            }
            if (!isset($attributes['data-placement'])) {
                $attributes['data-placement'] = 'top';
            }
            $attributes['title'] = __($this->_formName . '_' . $name . '_desc');
            return parent::label($name, $attributes);
        } else {
            return parent::label($name, $attributes);
        }
    }

    /**
     * Get SEO form HTML
     *
     * @param bool $useCol
     * @param string $cols
     * @param bool $clearFix
     * @param string $title
     * @return string
     */
    public function getSeoFormHTML($useCol = true, $cols = 'col-md-6', $clearFix = false, $title = 'SEO Info')
    {
        $html = '';
        if ($useCol) {
            $html = '<div class="' . $cols . '">';
        }

        if ($title) {
            //$html .= '<h4 class="seo-form-title">' . $title . '</h4>';
        }

        //Render element title
        $html .= '<div class="form-group">
                     <label class="control-label">' . __('gb_seo_label_seo_title') . '</label>' .
            $this->render('zcms_seo_title') .
            '</div>';

        //Render element description
        $html .= '<div class="form-group">
                     <label class="control-label">' . __('gb_seo_label_meta_desc') . '</label>' .
            $this->render('meta_desc') .
            '</div>';

        //Render element description
        $html .= '<div class="form-group">
                     <label class="control-label">' . __('gb_seo_label_meta_keywords') . '</label>' .
            $this->render('meta_keywords') .
            '</div>';

        //Render element robot index
        $html .= '<div class="form-group">
                     <label class="control-label">' . __('gb_seo_label_meta_robot_index') . '</label>' .
            $this->render('zcms_meta_robot_index') .
            '</div>';

        //Render element robot follow
        $html .= '<div class="form-group">
                     <label class="control-label">' . __('gb_seo_label_meta_robot_follow') . '</label>' .
            $this->render('zcms_meta_robot_follow') .
            '</div>';

        //Render element robot advance
//       $html .= '<div class="form-group">
//                    <label class="control-label">' . __('gb_seo_label_meta_robot_advance') . '</label>' .
//           $this->render('zcms_meta_robot_advance') .
//           '</div>';

        //Render element robot advance
        $html .= '<div class="form-group">
                     <label class="control-label">' . __('gb_seo_label_redirect_301') . '</label>' .
            $this->render('zcms_redirect_301') .
            '</div>';

        if ($useCol) {
            if ($clearFix) {
                $html .= '</div><div class="clearfix"></div>';
            } else {
                $html .= '</div>';
            }
        }

        return $html;
    }

    /**
     * Add element to form
     *
     * @param FElementInterface $element
     * @param string $position
     * @param bool $type If $type is TRUE, the element wile add before $position, else is after
     * @return \ZCMS\Core\Forms\ZForm
     */
    public function add(FElementInterface $element, $position = null, $type = null)
    {
        if ($this->bootstrap) {
            $class = $element->getAttribute("class");
            $classes = array_map("trim", explode(" ", $class));
            if (!in_array("form-control", $classes)) {
                $element->setAttribute("class", "form-control " . $class);
            }
        }
        if ($this->_autoGenerateTranslateLabel && $this->_formName != null) {
            $title = __($this->_formName . '_' . $element->getName());
            $attributes = $element->getAttributes();
            if (isset($attributes['required'])) {
                $title .= ' <span class="symbol required"></span>';
            }
            $element->setLabel($title);
        }
        return parent::add($element, $position, $type);
    }

    /**
     * Overwrite bind function
     *
     * @param array $data
     * @param object $entity
     * @param array $whiteList
     * @return Form
     */
    public function bind(array $data, $entity, $whiteList = null)
    {
        if ($this->_titleColumn != '') {
            $data = $this->repaidSEOData($data);
            unset($data['zcms_seo_title']);
            unset($data['zcms_meta_robot_advance']);
            unset($data['zcms_meta_robot_follow']);
            unset($data['zcms_meta_robot_index']);
            unset($data['zcms_metadata']);
            unset($data['zcms_redirect_301']);
        }
        return parent::bind($data, $entity, $whiteList);
    }
}