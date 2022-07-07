<?php
namespace RA\Auth\Validators;

use RA\Auth\Models\User as Model;

class ForgotPasswordValidator
{
    public static function run($data) {
        //validate request params
        $validator = \Validator::make($data, [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        $item = Model::where('email', $data['email'])->first();

        if ( !$item ) {
            return 'User not found.';
        }

        return true;
    }
}
