<?php

namespace ZCMS\Backend\Content\Forms;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\InclusionIn;
use ZCMS\Core\Forms\ZForm;
use ZCMS\Core\Models\Behavior\SEOTable;
use ZCMS\Core\Models\PostCategory;

class CategoryForm extends ZForm
{
    use SEOTable;

    /**
     * Title column for SEO
     *
     * @var string
     */
    public $_titleColumn = 'title';

    /**
     * @var string
     */
    public $_formName = 'm_content_form_category_form';

    /**
     * Init form
     *
     * @param \ZCMS\Core\Models\PostCategory $category
     * @param array $options
     */
    public function initialize($category = null, $options = [])
    {
        $this->buildSEOForm($category);

        $title = new Text('title', ['required' => 'required']);
        $this->add($title);

        $alias = new Text('alias');
        $this->add($alias);

        $published = new Select('published', [
            '1' => __('gb_published'),
            '0' => __('gb_unpublished')
        ], ['value' => $category != null ? $category->published : 'published']);
        $this->add($published);

        $description = new TextArea('description', ['class' => 'summernote']);
        $this->add($description);

        $categories = PostCategory::getTree('content');
        $categoryFilter = array();

        $categoryFilter[''] = __('gb_select');
        foreach ($categories as $index => $cat) {
            $pad = str_pad('', 2 * $cat->level, '- ', STR_PAD_LEFT);
            $categoryFilter[$cat->category_id] = $pad . ' ' . $cat->title;
        }

        if (isset($options['edit'])) {
            $parent = $category->parent();
            if($parent){
                $value = $parent->category_id;
            }else{
                $root = PostCategory::getRoot('content');
                $value = $root->category_id;
            }
            $elementParent = new Select('parent', $categoryFilter,['value' => $value, 'required' => 'required']);
        } else {
            $elementParent = new Select('parent', $categoryFilter, ['required' => 'required']);
        }

        /**
         * @var \Phalcon\Mvc\Model\ResultsetInterface $categories
         */
        $elementParent->addValidator(new InclusionIn(array(
            'domain' => array_column($categories->toArray(), 'category_id')
        )));
        $this->add($elementParent);
    }
}