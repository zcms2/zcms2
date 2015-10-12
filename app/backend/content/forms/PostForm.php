<?php

namespace ZCMS\Backend\Content\Forms;

use ZCMS\Core\Forms\ZForm;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use ZCMS\Core\Models\PostCategory;
use Phalcon\Forms\Element\TextArea;
use ZCMS\Core\Models\Behavior\SEOTable;
use Phalcon\Validation\Validator\InclusionIn;

/**
 * Class PostForm
 *
 * @package ZCMS\Backend\Content\Forms
 */
class PostForm extends ZForm
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
    public $_formName = 'm_content_form_post_form';

    /**
     * Init form
     *
     * @param \ZCMS\Core\Models\Posts $post
     * @param array $options
     */
    public function initialize($post = null, $options = [])
    {
        //Build SEO form
        $this->buildSEOForm($post);

        $title = new Text('title', ['required' => 'required']);
        $this->add($title);

        $alias = new Text('alias');
        $this->add($alias);

        $published = new Select('published', [
            '1' => __('gb_published'),
            '0' => __('gb_unpublished')
        ], ['value' => $post != null ? $post->published : 1]);
        $this->add($published);

        $intro_text = new TextArea('intro_text', ['rows' => 3]);
        $this->add($intro_text);

        $full_text = new TextArea('full_text', ['class' => 'summernote']);
        $this->add($full_text);

        $commentStatus = new Select('comment_status', [
            '1' => __('gb_open'),
            '0' => __('gb_close')
        ], ['value' => $post != null ? $post->comment_status : 1]);
        $this->add($commentStatus);

        $categories = PostCategory::getTree('content');
        $categoryFilter = array();

        $categoryFilter[''] = __('gb_select');
        foreach ($categories as $index => $cat) {
            $pad = str_pad('', 2 * $cat->level, '- ', STR_PAD_LEFT);
            $categoryFilter[$cat->category_id] = $pad . ' ' . $cat->title;
        }

        if (isset($options['edit'])) {
            $elementParent = new Select('category_id', $categoryFilter, ['value' => $post->category_id, 'required' => 'required']);
        } else {
            $elementParent = new Select('category_id', $categoryFilter, ['required' => 'required']);
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