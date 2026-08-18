<?php
namespace RA\Auth\Domains\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Auth\Services\ClassName;

class DeleteMemberAction extends Action
{
    public function run($team_id, $id) {
        $item = ClassName::Model('TeamMember')::find($id);

        //validate request
        $validation = ClassName::Validator('Team\DeleteMemberValidator')::run($item);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $this->ensureTeam($item, $team_id);
        $item->delete();

        return Response::success();
    }

    private function ensureTeam($item, $team_id) {
        $team_member = ClassName::Model('TeamMember')::where('user_id', $item->user_id)
            ->where('team_id', '!=', $team_id)
            ->first();

        if ( $team_member ) {
            return;
        }

        //user is not part of any teams, create a default one
        $team = ClassName::Model('Team')::create([
            'uuid' => \Str::uuid(),
            'created_by' => $item->user_id,
            'name' => 'My Team',
        ]);

        //insert user in own team
        ClassName::Model('TeamMember')::create([
            'team_id' => $team->id,
            'user_id' => $item->user_id,
            'role' => 'owner',
        ]);
    }
}
