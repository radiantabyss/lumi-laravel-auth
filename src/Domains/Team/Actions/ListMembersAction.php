<?php
namespace Lumi\Auth\Domains\Team\Actions;

use Illuminate\Routing\Controller as Action;
use Lumi\Core\Response;
use Lumi\Core\Filter;
use Lumi\Auth\Services\ClassName;

class ListMembersAction extends Action
{
    public function run($team_id) {
        if ( \Gate::denies('manage-team', $team_id) ) {
            return Response::error('Sorry, you can\'t view this team\'s members.');
        }

        //get query
        $query = ClassName::Model('TeamMember')::select('id', 'user_id', 'role', 'created_at')
            ->with('user:id,email,name')
            ->where('team_id', $team_id);

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        //paginate
        $paginated = $query->paginate(config('settings.per_page'));
        $items = ClassName::Presenter('Team\ListMembersPresenter')::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        //get invites
        $invites = ClassName::Model('TeamInvite')::where('team_id', $team_id)->get();

        return Response::success(compact('items', 'total', 'pages', 'invites'));
    }
}
