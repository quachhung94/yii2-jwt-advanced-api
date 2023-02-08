<?php
/**
 * Created by HanumanIT Co.,Ltd.
 * User: kongoon
 * Date: 8/11/2018 AD
 * Time: 12:49
 */

namespace api\modules\v1\controllers;


use api\components\Controller;
use api\models\forms\RegisterForm;
use yii\filters\AccessControl;
use api\models\forms\LoginForm;
use Yii;
use api\models\UserRefreshToken;
use api\models\User;

class GuestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'login', 'register', 'refresh'],
                        'allow' => true,
                    ],
                ],
            ]
        ];
    }
    /**
     * @SWG\Definition(
     *   definition="About",
     *   type="object",
     *   required={"name", "description", "version", "baseUrl"},
     *   allOf={
     *     @SWG\Schema(
     *       @SWG\Property(property="name", type="string", description="Name App"),
     *       @SWG\Property(property="description", type="string", description="Detail Information App"),
     *       @SWG\Property(property="version", type="string", description="Version APP"),
     *       @SWG\Property(property="baseUrl", type="string", description="Base Url APP")
     *     )
     *   }
     * )
     */
    public function actionIndex()
    {
        $params = Yii::$app->params;
        return [
            'name' => $params['name'],
            'description' => $params['description'],
            'version' => $params['version'],
            'baseUrl' => $this->baseUrl()
        ];
    }
    /**
     * Login
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->loadData(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->identity;

            $token = $this->generateJwt($user);

            $refresh_token = $this->generateRefreshToken($user);

            $ret = [
                'user' => $user,
                'token' => (string) $token,
                'refresh_token' => $refresh_token,
            ];
            return $this->apiItem($ret);
        } else {
            return $this->apiValidate($model->getFirstErrors());
        }
        
    }

    /**
     * @return string[]|\yii\web\ServerErrorHttpException|\yii\web\UnauthorizedHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRefresh() {
        $refreshToken = Yii::$app->request->getBodyParams();

        if (!$refreshToken['urf_token']) {
            return new \yii\web\UnauthorizedHttpException('No refresh token found.');
        }

        $userRefreshToken = UserRefreshToken::findOne(['urf_token' => $refreshToken['urf_token']]);

        if (Yii::$app->request->getMethod() == 'POST') {
            if (!$userRefreshToken) {
                return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
            }

            $user = User::findOne(['id' => $userRefreshToken->urf_userID, 'status' => 10]);

            if (!$user) {
                $userRefreshToken->delete();
                return new \yii\web\UnauthorizedHttpException('The user is inactive.');
            }

            $token = $this->generateJwt($user);
            $ret = [
                $user,
                'token' => (string) $token,
            ];
            
            return $this->apiItem($ret);

        } elseif (Yii::$app->request->getMethod() == 'DELETE') {
            // Logging out
            if ($userRefreshToken && !$userRefreshToken->delete()) {
                return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
            }

            return ['status' => 'ok'];
        } else {
            return new \yii\web\UnauthorizedHttpException('The user is inactive.');
        }
    }

    /**
     * @param User $user
     * @return mixed
     */
    private function generateJwt(User $user)
    {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $jwtParams = Yii::$app->params['jwt'];

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('id', $user->id)
            ->getToken($signer, $key);
    }

    /**
     * @param User $user
     * @throws \yii\base\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    private function generateRefreshToken(User $user)
    {
        $refreshToken = Yii::$app->security->generateRandomString(200);

        // TODO: Don't always regenerate - you could reuse existing one if user already has one with same IP and user agent
        $userRefreshToken = new UserRefreshToken([
            'urf_userID' => $user->id,
            'urf_token' => $refreshToken,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent,
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $userRefreshToken->getErrorSummary(true));
        }

        return $userRefreshToken;

    }

    /**
     * Register
     *
     * @return mixed
     */
    public function actionRegister()
    {
        $dataRequest['RegisterForm'] = Yii::$app->request->getBodyParams();
        $model = new RegisterForm();
        if ($model->load($dataRequest)) {
            if ($user = $model->register()) {
                return $this->apiRegister($user);
            }
        }
        return $this->apiValidate($model->errors);
    }
}