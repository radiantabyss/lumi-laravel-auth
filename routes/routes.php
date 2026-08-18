<?php
use RA\Route;

Route::post('/auth/user/accept-invite', 'User\AcceptInviteAction');

//no auth
Route::group(['middleware' => ['RA\Auth\NotLogged']], function() {
    Route::post('/auth/user/register', 'User\RegisterAction');
    Route::post('/auth/user/forgot-password', 'User\ForgotPasswordAction');
    Route::post('/auth/user/reset-password', 'User\ResetPasswordAction');
    Route::post('/auth/user/confirm', 'User\ConfirmAction');
    Route::post('/auth/user/login', 'User\LoginAction');
});

//with auth
Route::group(['middleware' => ['RA\Auth\Logged']], function() {
    Route::get('/auth/user/get', 'User\GetAction');
    Route::post('/auth/user/patch', 'User\PatchAction');
    Route::get('/auth/user/delete', 'User\DeleteAction');
    Route::post('/auth/user/upload-profile-image', 'User\UploadProfileImageAction');
    Route::options('/auth/user/upload-profile-image', 'User\UploadProfileImageAction');

    Route::get('/auth/team', 'Team\ListAction');
    Route::post('/auth/team/create', 'Team\CreateAction');
    Route::post('/auth/team/single/{team_id}', 'Team\SingleAction');
    Route::post('/auth/team/switch/{team_id}', 'Team\SwitchAction');
    Route::get('/auth/team/leave/{team_id}', 'Team\LeaveAction');
});

//manage team
Route::group(['middleware' => ['RA\Auth\Logged', 'RA\Auth\TeamRole:owner']], function() {
    Route::get('/auth/team/edit/{team_id}', 'Team\EditAction');
    Route::post('/auth/team/update/{team_id}', 'Team\UpdateAction');
    Route::post('/auth/team/patch/{team_id}', 'Team\PatchAction');
    Route::get('/auth/team/delete/{team_id}', 'Team\DeleteAction');

    Route::post('/auth/team/upload-image/{team_id}', 'Team\UploadImageAction');
    Route::options('/auth/team/upload-image/{team_id}', 'Team\UploadImageAction');

    Route::post('/auth/team/change-role/{team_id}', 'Team\ChangeRoleAction');
    Route::get('/auth/team/list-members/{team_id}', 'Team\ListMembersAction');
    Route::get('/auth/team/delete-member/{team_id}/{id}', 'Team\DeleteMemberAction');

    Route::post('/auth/team/invite/{team_id}', 'Team\InviteAction');
    Route::post('/auth/team/resend-invite/{team_id}/{invite_id}', 'Team\ResendInviteAction');
    Route::get('/auth/team/delete-invite/{team_id}/{id}', 'Team\DeleteInviteAction');
});
