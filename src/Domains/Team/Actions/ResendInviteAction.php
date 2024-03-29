<?php
namespace Lumi\Auth\Domains\Team\Actions;

use Illuminate\Routing\Controller as Action;
use Lumi\Core\Response;
use Lumi\Core\MailSender;
use Lumi\Auth\Services\ClassName;
use Lumi\Auth\Domains\Team\Mail\InviteMail;

class ResendInviteAction extends Action
{
    public function run($invite_id) {
        ClassName::Model('TeamInvite')::where('id', $invite_id)->update([
            'code' => \Str::random(30),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+2 hours')),
        ]);

        $invite = ClassName::Model('TeamInvite')::with('team')->find($invite_id);

        if ( !$invite ) {
            return Response::error('Invite not found.');
        }

        //send mail
        MailSender::run(InviteMail::class, $invite->email, [
            'team' => $invite->team,
            'invite' => $invite,
        ]);

        return Response::success(compact('invite'));
    }
}
