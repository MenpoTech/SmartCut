<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mst_users".
 *
 * @property integer $id
 * @property integer $mst_institution_id
 * @property string $user_type
 * @property integer $userref_id
 * @property string $username
 * @property string $password
 * @property string $displayname
 * @property integer $is_common_user
 * @property integer $status
 * @property integer $is_deleted
 * @property string $created_date
 * @property integer $created_by
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $usr_last_access_ip
 * @property string $usr_last_access
 * @property string $user_email_id
 * @property string $reset_password_token
 * @property string $token_created_at
 * @property string $usr_status
 * @property integer $first_visited
 * @property string $second_password
 * @property string $email_confirm_token
 * @property integer $is_email_confirm
 * @property string $tmp_password
 * @property integer $is_account_activated
 * @property integer $is_mail_avail
 * @property integer $login_attempt_count
 * @property integer $is_account_locked
 * @property string $station_id
 * @property integer $mst_feeheading_id
 * @property integer $ext_no
 */
class MstUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mst_institution_id', 'userref_id', 'is_common_user', 'status', 'is_deleted', 'created_by', 'modified_by', 'first_visited', 'is_email_confirm', 'is_account_activated', 'is_mail_avail', 'login_attempt_count', 'is_account_locked', 'mst_feeheading_id', 'ext_no'], 'integer'],
            [['created_date', 'modified_date', 'usr_last_access', 'token_created_at'], 'safe'],
            [['reset_password_token', 'email_confirm_token', 'station_id'], 'string'],
            [['user_type', 'username', 'password', 'displayname', 'user_email_id', 'second_password', 'tmp_password'], 'string', 'max' => 150],
            [['usr_last_access_ip'], 'string', 'max' => 100],
            [['usr_status'], 'string', 'max' => 5],
            [['ext_no'], 'string', 'min'=>4,'max' => 4],
            [['username'], 'unique','targetAttribute' => ['usernameLowercase' => 'lower(username)']],
            ['ext_no', 'unique'],
            [['username','ext_no','password','displayname'], 'required'],
            [['username','ext_no','password','displayname'], 'filter', 'filter'=>'trim'],
        ];
    }

    public function getUsernameLowercase()
    {
        return strtolower($this->username);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mst_institution_id' => 'Mst Institution ID',
            'user_type' => 'User Type',
            'userref_id' => 'Userref ID',
            'username' => 'Username',
            'password' => 'Password',
            'displayname' => 'Displayname',
            'is_common_user' => 'Is Common User',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'usr_last_access_ip' => 'Usr Last Access Ip',
            'usr_last_access' => 'Usr Last Access',
            'user_email_id' => 'User Email ID',
            'reset_password_token' => 'Reset Password Token',
            'token_created_at' => 'Token Created At',
            'usr_status' => 'Usr Status',
            'first_visited' => 'First Visited',
            'second_password' => 'Second Password',
            'email_confirm_token' => 'Email Confirm Token',
            'is_email_confirm' => 'Is Email Confirm',
            'tmp_password' => 'Tmp Password',
            'is_account_activated' => 'Is Account Activated',
            'is_mail_avail' => 'Is Mail Avail',
            'login_attempt_count' => 'Login Attempt Count',
            'is_account_locked' => 'Is Account Locked',
            'station_id' => 'Station ID',
            'mst_feeheading_id' => 'Mst Feeheading ID',
            'ext_no' => 'PIN No',
        ];
    }

    function getNewPin() {
        $pin = rand(999,9999);
        $cnt = MstUser::find()->where(['ext_no'=>$pin])->count();
        if($cnt){
            return $this->getNewPin();
        }else {
            return $pin;
        }
    }

    function validatePin($pin) {
        return MstUser::find()->where(['ext_no'=>$pin])->count();
    }
}
