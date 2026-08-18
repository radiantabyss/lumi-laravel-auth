<?php
namespace RA\Auth\Domains\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Auth\Services\ClassName;
use RA\Auth\Events\TeamDeleted;
use RA\Auth\Services\Jwt;

class DeleteAction extends Action
{
    public function run($team_id) {
        $item = ClassName::Model('Team')::find($team_id);

        if ( !$item ) {
            return Response::error('Team not found.');
        }

        //delete meta
        ClassName::Model('TeamMeta')::where('team_id', $team_id)->delete();

        //delete members
        ClassName::Model('TeamMember')::where('team_id', $team_id)->delete();

        $item->delete();

        //let the app clean up after a deleted team (queue via a ShouldQueue listener if needed)
        event(new TeamDeleted($item));

        //switch team if is current team
        $response = $this->handleCurrentTeam($team_id);

        return Response::success($response);
    }

    private function handleCurrentTeam($team_id) {
        if ( \Auth::user()->team->id != $team_id ) {
            return null;
        }

        $team_member = ClassName::Model('TeamMember')::where('user_id', \Auth::user()->id)->first();

        if ( $team_member ) {
            $team_id = $team_member->team_id;
        }
        //user is not part of any teams, create a default one
        else {
            //create default team
            $team = ClassName::Model('Team')::create([
                'uuid' => \Str::uuid(),
                'created_by' => \Auth::user()->id,
                'name' => 'My Team',
            ]);

            //insert user in own team
            ClassName::Model('TeamMember')::create([
                'team_id' => $team->id,
                'user_id' => \Auth::user()->id,
                'role' => 'owner',
            ]);

            $team_id = $team->id;
        }

        //regenerate jwt token
        $user = ClassName::Presenter('User\Presenter')::run(\Auth::user(), $team_id);
        $jwt_token = Jwt::generate(ClassName::Presenter('User\JwtPresenter')::run(clone $user));

        return compact('user', 'jwt_token');
    }
}
