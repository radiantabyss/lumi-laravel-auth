<?php
namespace Lumi\Auth;

use Illuminate\Support\ServiceProvider;
use Lumi\Auth\Services\ClassName;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //enable publishing
        $this->enablePublishing();

        //register view domains
        \View::addNamespace('Lumi.Auth.Team', __DIR__.'/Domains/Team/Mail/views');
        \View::addNamespace('Lumi.Auth.User', __DIR__.'/Domains/User/Mail/views');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //load configs
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'ra-auth');

        //register middleware
        $this->registerMiddleware();

        //register gates
        $this->registerGates();
    }

    private function registerMiddleware() {
        $router = $this->app['router'];
        $router->aliasMiddleware('Lumi\Auth\Logged', \Lumi\Auth\Http\Middleware\LoggedMiddleware::class);
        $router->aliasMiddleware('Lumi\Auth\NotLogged', \Lumi\Auth\Http\Middleware\NotLoggedMiddleware::class);
        $router->aliasMiddleware('Lumi\Auth\SetUser', \Lumi\Auth\Http\Middleware\SetUserMiddleware::class);
        $router->aliasMiddleware('Lumi\Auth\TeamRole', \Lumi\Auth\Http\Middleware\TeamRoleMiddleware::class);
        $router->aliasMiddleware('Lumi\Auth\UserType', \Lumi\Auth\Http\Middleware\UserTypeMiddleware::class);
    }

    private function registerGates() {
        \Gate::define('owns-team', function($user, $team_id = null) {
            if ( $user->type == 'super_admin' ) {
                return true;
            }

            return ClassName::Model('TeamMember')::where('team_id', $team_id ?: $user->team->id)
                ->where('user_id', $user->id)
                ->where('role', 'owner')
                ->exists();
        });

        \Gate::define('manage-team', function($user, $team_id = null) {
            if ( $user->type == 'super_admin' ) {
                return true;
            }

            return ClassName::Model('TeamMember')::where('team_id', $team_id ?: $user->team->id)
                ->where('user_id', $user->id)
                ->whereIn('role', ['owner', 'admin'])
                ->exists();
        });
    }

    private function enablePublishing() {
        if ( !\App::runningInConsole() ) {
            return;
        }

        //config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('ra-auth.php'),
        ], 'ra-auth:config');

        //actions
        $this->publishes([
            __DIR__.'/Domains/Auth/Actions' => app_path('Domains/Auth/Actions'),
        ], 'ra-auth:actions');

        //commands
        $this->publishes([
            __DIR__.'/Domains/Auth/Commands' => app_path('Domains/Auth/Commands'),
        ], 'ra-auth:commands');

        //mail
        $this->publishes([
            __DIR__.'/Domains/Auth/Mail' => app_path('Domains/Auth/Mail'),
        ], 'ra-auth:mail');

        //presenters
        $this->publishes([
            __DIR__.'/Domains/Auth/Presenters' => app_path('Domains/Auth/Presenters'),
        ], 'ra-auth:presenters');

        //transformers
        $this->publishes([
            __DIR__.'/Domains/Auth/Transformers' => app_path('Domains/Auth/Transformers'),
        ], 'ra-auth:transformers');

        //validators
        $this->publishes([
            __DIR__.'/Domains/Auth/Validators' => app_path('Domains/Auth/Validators'),
        ], 'ra-auth:validators');

        //routes
        $this->publishes([
            __DIR__.'/../routes/routes.php' => base_path('routes/ra-auth.php'),
        ], 'ra-auth:routes');

        //migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => base_path('database/migrations'),
        ], 'ra-auth:migrations');
    }
}
