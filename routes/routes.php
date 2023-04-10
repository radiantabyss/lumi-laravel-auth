<?php
use RA\Core\Route;

//no auth
Route::group(['middleware' => ['RA\Auth\NoAuth']], function() {
    Route::post('/register', 'RegisterAction');
    Route::post('/forgot-password', 'ForgotPasswordAction');
    Route::post('/reset-password', 'ResetPasswordAction');
    Route::post('/confirm', 'ConfirmAction');
    Route::post('/login', 'LoginAction');

    Route::post('/accept-invite', 'AcceptInviteAction');
});

//with auth
Route::group(['middleware' => ['RA\Auth\Auth']], function() {
    Route::get('/get', 'GetAction');
    Route::post('/patch', 'PatchAction');
    Route::post('/upload-logo', 'UploadLogoAction');
    Route::options('/upload-logo', 'UploadLogoAction');

    Route::post('/create-team', 'CreateTeamAction');
    Route::post('/update-team/{team_id}', 'UpdateTeamAction');
    Route::post('/switch-team/{team_id}', 'SwitchTeamAction');
    Route::post('/invite', 'InviteAction');
});
