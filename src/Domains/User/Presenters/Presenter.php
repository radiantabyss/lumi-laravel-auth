<?php
namespace RA\Auth\Domains\User\Presenters;

use RA\Auth\Services\ClassName;

class Presenter
{
    public static function run($item, $team_id = null) {
        //load meta
        $item->loadMeta();

        //remove unwanted user fields
        unset($item->password);
        unset($item->created_at);
        unset($item->updated_at);

        //load current or first team and role
        $team = ClassName::Model('UserTeam')::select('user_team.*', 'user_team_member.role')
            ->leftJoin('user_team_member', 'user_team_member.team_id', '=', 'user_team.id')
            ->where('user_team_member.user_id', $item->id)
            ->where(function($query) use($team_id) {
                if ( $team_id ) {
                    $query->where('user_team.id', $team_id);
                }
            })
            ->orderBy('id')
            ->first();

        if ( $team ) {
            $team->loadMeta();
        }

        $item->team = $team;

        return $item;
    }
}