<?php
namespace Lumi\Auth\Domains\Team\Validators;

use Lumi\Auth\Services\ClassName;

class SwitchValidator
{
    public static function run($team_id) {
        //check if team exists
        $tean = ClassName::Model('Team')::find($team_id);
        if ( !$tean ) {
            return 'Team not found.';
        }

        //check if is user is in team
        $exists = ClassName::Model('TeamMember')::where('team_id', $team_id)
            ->where('user_id', \Auth::user()->id)
            ->exists();

        if ( !$exists ) {
            return 'You\'re not part of this team';
        }

        return true;
    }
}
