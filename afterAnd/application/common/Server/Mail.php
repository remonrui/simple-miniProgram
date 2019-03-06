<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 13:16
 */

namespace app\common\Server;

use PHPMailer\PHPMailer\PHPMailer;


/*
 * 邮件
 * */
class Mail
{
    /*
     *邮件发送 */
    public  static function send($to, $subject, $body) {

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->CharSet='UTF-8';
        $mail->Host = config('mail.mail_smtp');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.mail_username');
        $mail->Password = config('mail.mail_password');
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom(config('mail.mail_username'), '微信流量团队');
        $mail->addAddress($to);
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $body; // no-html client

        if($mail->send()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 密码找回
     * */
    public static function sendForgotPassword($email, $token) {
        $url = config('web.host').'/'.config('mail.mail_pass_reset').'?token='.$token;
        $body = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <link href="'.config('web_url').'/admin/css/email.css" rel="stylesheet">
        </head>
        <body>
        <div class="mail-container" style="    position: relative; width: 620px;font-size: 14px;margin: 0 auto;">
            <div class="logo" style="height: 40px;font-size: 30px;line-height: 30px;">
            <span style="display: block;position: absolute;top: 2px;left: 36px;">Email地址验证</span>
            </div>
            <div class="mail-content" style="   width: 100%;border-top: 1px solid #999;border-bottom: 1px solid #999;">
                <p>如果您忘记了<strong>后台</strong>的密码</p>
                <p>请点击以下链接重置你的密码</p>
                <div class="mail-url" style="  word-break: break-all; width: 600px;background-color: #82b95e; border-radius: 2px; padding: 10px 10px;">
                    <span><a href="'.$url.'">'.$url.'</a></span>
                </div>
                <p>密码重置链接将在48小时后失效。</p>
                <p>请勿回复此邮件</p>
                <p class="font-email">--</p>
            </div>
            <p class="font-email" style="color: #999">如果你没有注册过，请忽略此邮件</p>
        </div>
        </body>
        </html>';

        return self::send($email, "密码找回", $body);
    }

//    /**
//     * 后台注册关闭
//     * @param $email
//     * @param $token
//     * @return bool
//     */
//    public static function sendActiveUser($email,$token) {
//        $url = config('web.host').'/'.config('mail.mail_check_url').'?token='.$token;
//
//        $body = '<!DOCTYPE html>
//        <html lang="en">
//        <head>
//            <meta charset="UTF-8">
//            <link href="'.config('web_url').'/admin/css/email.css" rel="stylesheet">
//        </head>
//        <body>
//        <div class="mail-container" style="    position: relative; width: 620px;font-size: 14px;margin: 0 auto;">
//            <div class="logo" style="height: 40px;font-size: 30px;line-height: 30px;">
//
//                <span style="display: block;position: absolute;top: 2px;left: 36px;">Email地址验证</span>
//            </div>
//            <div class="mail-content" style="   width: 100%;border-top: 1px solid #999;border-bottom: 1px solid #999;">
//                <p>感谢您注册<strong>后台</strong></p>
//                <p>请点击以下链接激活你的帐号</p>
//                <div class="mail-url" style="  word-break: break-all; width: 600px;background-color: #82b95e; border-radius: 2px; padding: 10px 10px;">
//                    <span><a href="'.$url.'">'.$url.'</a></span>
//                </div>
//                <p>密码激活链接将在48小时后失效。</p>
//                <p>请勿回复此邮件</p>
//                <p class="font-email">--</p>
//            </div>
//            <p class="font-email" style="color: #999">如果你没有注册过，请忽略此邮件</p>
//        </div>
//        </body>
//        </html>';
//
//        return self::send($email, "帐号激活", $body);
//    }
}