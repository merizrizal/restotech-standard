<?php
namespace restotech\standard\common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'User ID',
            'password' => 'Password',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $notActive = false;        
        
        if ($this->validate() && !($notActive = $this->getUser()->not_active)) {
            if (Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0)) {                
                
                $modelUser = User::find()
                        ->joinWith([
                            'kdKaryawan',
                            'userLevel',
                            'userLevel.defaultAction',
                        ])
                        ->andWhere(['user.id' => Yii::$app->user->getIdentity()->id])
                        ->asArray()->one();
                                
                $data['employee']['nama'] = $modelUser['kdKaryawan']['nama'];
                $data['employee']['image'] = $modelUser['kdKaryawan']['image'];
                $data['user_level']['id'] = $modelUser['userLevel']['id'];
                $data['user_level']['nama_level'] = $modelUser['userLevel']['nama_level'];                                   
                $data['user_level']['is_super_admin'] = $modelUser['userLevel']['is_super_admin'];
                
                $subProgram = '';
                $namaModule = '';
                $moduleAction = '';
                
                if (!empty($modelUser['userLevel']['defaultAction'])) {
                    $subProgram = $modelUser['userLevel']['defaultAction']['sub_program'];
                    $namaModule = $modelUser['userLevel']['defaultAction']['nama_module'];
                    $moduleAction = $modelUser['userLevel']['defaultAction']['module_action'];
                } else {
                    $subProgram = 'administrator';
                    $namaModule = 'user';
                    $moduleAction = 'index';
                }
                
                if (!Yii::$app->params['subdomain']) {
                    
                    $rootUrl = Yii::getAlias('@rootUrl') . '/';

                    if ($subProgram == '/') {
                        $data['user_level']['default_action'] = $rootUrl . $namaModule . '/' . $moduleAction;
                    } else {
                        
                        if (!empty($subProgram))
                            $subProgram = !empty(Yii::$app->params['subprogram'][$subProgram]) ?  Yii::$app->params['subprogram'][$subProgram] . '/' : '';

                        $data['user_level']['default_action'] = $rootUrl . $subProgram . $namaModule . '/' . $moduleAction;
                    }
                } else {
                    
                    $rootUrl = Yii::getAlias('@rootUrl');

                    if ($subProgram == '/') {
                        $data['user_level']['default_action'] = $rootUrl . $namaModule . '/' . $moduleAction;
                    } else {
                        
                        $rootUrl = str_replace('http://', '', $rootUrl) . '/';
                        
                        if (!empty($subProgram))
                            $subProgram = Yii::$app->params['subprogram'][$subProgram] . '.';

                        $data['user_level']['default_action'] = 'http://' . $subProgram . $rootUrl . $namaModule . '/' . $moduleAction;
                    }
                }
                
                $userAkses = \restotech\standard\backend\models\UserAkses::find()
                        ->joinWith(['userLevel', 'userAppModule'])
                        ->andWhere(['user_akses.user_level_id' => $data['user_level']['id']])
                        ->andWhere(['user_akses.is_active' => true])
                        ->asArray()->all();
                
                $data['user_level']['userAkses'] = $userAkses;       
                
                Yii::$app->session->set('user_data', $data);
                
                return true;
            }
        } else {
            if ($notActive)
                $this->addError('username', 'This user is not active');
                
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
