<?php
namespace ZCMS\Backend\User\Forms;

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Email;
use Phalcon\Validation\Validator\Confirmation;
use ZCMS\Core\Forms\ZForm;

/**
 * Class UserProfileForm
 *
 * @package ZCMS\backend\user\forms
 */
class UserProfileForm extends ZForm
{

    /**
     * @var string
     */
    public $_formName = 'm_user_form_user_profile';

    /**
     * Init form
     *
     * @param \ZCMS\Core\Models\Users $user
     */
    public function initialize($user = null)
    {
        //First name
        $firstName = new Text('first_name', [
            'maxlength' => '32',
            'required' => 'required'
        ]);
        $this->add($firstName);

        //Last name
        $lastName = new Text('last_name', [
            'maxlength' => '32',
            'required' => 'required'
        ]);
        $this->add($lastName);

        //Email
        $email = new Email('email', [
            'maxlength' => '128',
            'required' => 'required',
            'readonly' => 'readonly'
        ]);
        $this->add($email);

        //Current password
        $currentPassword = new Password('current_password', [
            'maxlength' => '32'
        ]);
        $this->add($currentPassword);

        //Password
        $password = new Password('password', [
            'maxlength' => '32'
        ]);
        $password->addValidator(new Confirmation([
            'message' => 'm_user_message_confirm_password_not_match',
            'with' => 'password_confirmation'
        ]));
        $this->add($password);

        //Password confirmation
        $password_confirmation = new Password('password_confirmation', [
            'maxlength' => '32'
        ]);
        $this->add($password_confirmation);
    }
}