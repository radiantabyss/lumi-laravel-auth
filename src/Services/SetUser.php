<?php
namespace RA\Auth\Services;

class SetUser
{
    public static function run($request) {
        //get jwt token from request
        $payload = $request->get(config('jwt.input'));

        //remove jwt token from request
        $request->query->remove(config('jwt.input'));

        if ( !$payload ) {
            return 'JWT Token is required.';
        }

        //validate token
        $token = Jwt::validate($payload);
        if ( $token === false ) {
            return 'JWT Token is invalid.';
        }

        //get user from token
        $user = ClassName::Model('User')::where('id', $token->id)
            ->where('uuid', $token->uuid)
            ->first();

        //check
        if ( !$user ) {
            return 'JWT Token is invalid.';
        }

        //format
        $user = ClassName::Presenter('User\Presenter')::run($user, $token->team_id);

        if ( !$user->team ) {
            return 'JWT Token is invalid.';
        }

        \Auth::setUser($user);

        return true;
    }
}
