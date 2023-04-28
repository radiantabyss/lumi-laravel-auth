<?php
namespace RA\Auth\Domains\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Core\Response;
use RA\Auth\Services\ClassName;

class ListAction extends Action
{
    public function run() {
        $items = ClassName::Model('UserTeam')::select('user_team.*', 'user_team_member.role', 'user_team_member.created_at as joined_at')
            ->leftJoin('user_team_member', 'user_team_member.team_id', '=', 'user_team.id')
            ->where('user_team_member.user_id', \Auth::user()->id)
            ->get();

        $items = ClassName::Presenter('Team\ListPresenter')::run($items);

        return Response::success(compact('items'));
    }
}