<?php
/**
 * @SWG\Definition(
 *     definition="ErrorValidate",
 *     required={"statusCode", "message", "name", "errors"},
 *     type="object",
 *     @SWG\Schema(
 *       @SWG\Property(property="statusCode", type="string", description="Name App"),
 *       @SWG\Property(property="message", type="string", description="Errors message"),
 *       @SWG\Property(property="name", type="string", description="Errors name"),
 *       @SWG\Property(property="errors", type="object", description="Errors Detail")
 *     )
 * )
 *
 * @SWG\Definition(
 *     definition="Unauthorized",
 *     required={"statusCode", "message", "name"},
 *     type="object",
 *     @SWG\Schema(
 *       @SWG\Property(property="statusCode", type="string", description="Name App"),
 *       @SWG\Property(property="message", type="string", description="Errors message"),
 *       @SWG\Property(property="name", type="string", description="Errors name"),
 *     )
 * )
 *
 * @SWG\Definition(
 *     definition="Not Found",
 *     required={"statusCode", "message", "name"},
 *     type="object",
 *     @SWG\Schema(
 *       @SWG\Property(property="statusCode", type="string", description="Name App"),
 *       @SWG\Property(property="message", type="string", description="Errors message"),
 *       @SWG\Property(property="name", type="string", description="Errors name"),
 *     )
 * )
 */
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'noreply.resetaccess@gmail.com',
    'passwordResetTokenExpire' => 300,

    /* detail app */
    'name' => 'Rest API',
    'description' => 'Rest API with Yii2',
    'version' => 'Version 0.0.1',
    /* params for jwt */
    'secretKey' => 'restApiToken',
    'expiresIn' => '+5 hours',
    'jwt' => [
        'issuer' => 'http://api.example.com',
        'audience' => 'http://frontend.example.com',
        'id' => 'str',
        'expire' => 300000,
    ],
];
