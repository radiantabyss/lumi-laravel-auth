<?php
namespace Lumi\Auth\Domains\User\Actions;

use Illuminate\Routing\Controller as Action;
use Lumi\Core\MailSender;
use Lumi\Core\Response;
use Lumi\Auth\Services\ClassName;

class ForgotPasswordAction extends Action
{
    public function run() {
        $data = \Request::all();

        //validate request
        $validation = ClassName::Validator('User\ForgotPasswordValidator')::run($data);
        if ( $validation !== true) {
            return Response::error($validation);
        }

        //get user by email
        $item = ClassName::Model('User')::where('email', $data['email'])->first();

        //create reset code
        $code = ClassName::Model('UserCode')::create([
            'user_id' => $item->id,
            'type' => 'reset_password',
            'code' => \Str::random(30),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+20 minutes')),
        ]);

        //send mail
        MailSender::send(ClassName::Mail('User\ForgotPasswordMail'), $item->email, compact('item', 'code'));

        return Response::success();
    }
}
