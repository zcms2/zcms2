<?php

namespace ZCMS\Backend\System\Forms;

use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\StringLength;
use ZCMS\Core\Forms\ZForm;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;
use ZCMS\Core\Models\UserRoles;

/**
 * Class UserForm
 *
 * @package ZCMS\Backend\System\Forms
 */
class UserForm extends ZForm
{

    /**
     * Init user form
     *
     * @param \ZCMS\Core\Models\Users $data
     */
    public function initialize($data = null)
    {
        //Add first name
        $firstName = new Text('first_name', [
            'maxlength' => '32'
        ]);
        $firstName->addValidator(new PresenceOf());

        $this->add($firstName);

        //Add last name
        $lastName = new Text('last_name', [
            'maxlength' => '32'
        ]);
        $lastName->addValidator(new PresenceOf());
        $this->add($lastName);

        //Add email
        if ($data = null) {
            $email = new Email('email', [
                'maxlength' => '128',
                'readonly' => 'readonly'
            ]);
        } else {
            $email = new Email('email', [
                'maxlength' => '128'
            ]);
        }
        $this->add($email);

        //Add active
        $is_active = new  Select('is_active', [
            '1' => __('gb_yes'),
            '0' => __('gb_no')
        ]);
        $this->add($is_active);

        //Add password confirmation
        $password_confirmation = new Password('password_confirmation', [
            'maxlength' => '32'
        ]);
        $password_confirmation->addValidator(new StringLength([
            'min' => 6,
        ]));
        $this->add($password_confirmation);

        //Add password
        $password = new Password('password', [
            'maxlength' => '32',
        ]);
        $password->addValidator(new StringLength([
            'min' => 6,
        ]));
        $password->addValidator(new Confirmation([
            'message' => 'm_system_user_message_password_does_not_match_confirmation',
            'with' => 'password_confirmation'
        ]));
        $this->add($password);

        //Add role
        $dbRoles = UserRoles::find([
            'conditions' => 'is_super_admin = 0',
            'order' => 'is_default DESC'
        ]);
        $role = new Select('role_id', $dbRoles, [
            'using' => ['role_id', 'name']
        ]);
        $role->addValidator(new InclusionIn([
            'message' => 'm_system_user_message_please_choose_role',
            'domain' => array_column($dbRoles->toArray(), 'role_id')
        ]));
        $this->add($role);
    }
}