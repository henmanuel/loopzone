<?php
use Firebase\JWT\JWT;
class Auth
{
    private static $secret_key = 'Sdw1s9x8@adjnA@#Sjs#dAsdg*$*S&D&nja';
    private static $encrypt = ['HS256'];
    private static $aud = null;
    public static function SignIn($data)
    {
        $time = time();
        
        $token = array('exp' => $time + (60*0.2),'aud' => self::Aud(),'data' => $data);

        return JWT::encode($token, self::$secret_key);
    }
    public static function Check($token)
    {
        try{

            if(empty($token))
            {
                throw new Exception("Invalid token supplied.");
            }
            $decode = JWT::decode($token,self::$secret_key,self::$encrypt);
            if($decode->aud !== self::Aud())
            {
                throw new Exception("Invalid user logged in.");
            } 
        }catch (\Firebase\JWT\ExpiredException $e ) {
            return'expTk';
        }catch (\Firebase\JWT\SignatureInvalidException $e ){
            return 'signCorrup';
        }catch (\Exception $e ){
            return 'error';
        }
    }
    public static function GetData($token)
    {
        return JWT::decode($token,self::$secret_key,self::$encrypt)->data;
    }
    private static function Aud()
    {
        $aud = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        return sha1($aud);
    }
}