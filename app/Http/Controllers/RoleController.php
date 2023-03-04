<?php

namespace App\Http\Controllers;

use App\Helpers\FileManager;
use App\Notifications\Message;
use Illuminate\Http\Request;

use App\Role;
use App\Setting;

class RoleController extends Controller
{
    /**
     * Get all the roles with their permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Role::with('current_permissions')->with('available_permissions')->get();
    }
    /**
     * Reset the roles settings ( default permission ).
     *
     * @return \Illuminate\Http\Response
     */
    public function resetRoles()
    {
        foreach (Role::all() as $role) {
            $role->detach_all();
            $role->add_default_permissions();
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }

    /**
     * Save the roles settings.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function saveRoles(Request $request)
    {
        foreach ($request->roles as $client_role) {
            $role = \App\Role::where('id', $client_role['id'])->first();
            $role->detach_all();
            foreach ($client_role['current_permissions']  as $permission) {
                $role->current_permissions()->attach($permission['id']);
            }
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Grant a user the admin role.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function grantUserAdmin(Request $request)
    {
        $whoWantToMakeAdmin = \Auth::user();
        $whoWillHaveAdmin = \App\User::find($request->user_id);
        $adminRole = Role::where('name', 'admin')->first();
        if ($whoWantToMakeAdmin->is_admin === 1) {
            $whoWillHaveAdmin->roles()->attach($adminRole);
        }

        // logout user
        $whoWillHaveAdmin->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json($adminRole, 200);
    }
    /**
     * Remove the admin role from a user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function revokeUserAdmin(Request $request)
    {
        $whoWantToRemoveAdmin = \Auth::user();
        $whoWillLoseAdmin = \App\User::find($request->user_id);
        $adminRole = Role::where('name', 'admin')->first();
        if ($whoWantToRemoveAdmin->is_admin === 1) {
            $whoWillLoseAdmin->roles()->detach($adminRole);
        }
        // logout user
        $whoWillLoseAdmin->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json($adminRole, 200);
    }
    /**
     * Accept the user request: asking for an the artist account.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function acceptArtistAccountRequest(Request $request)
    {
        $notification = \DB::table('notifications')->where('id', $request->notification_id)->first();
        $notification_data = json_decode($notification->data);
        $artist = \App\Artist::find($notification_data->artist->id);
        $user =  \App\User::find($notification_data->user->id);
        //logout user
        $user->tokens->each(function ($token) {
            $token->delete();
        });
        $artist->user_id = $user->id;
        // get the role and attach it wit permissions
        $artist_role = Role::where('name', 'artist')->first();
        $user->roles()->attach($artist_role->id);
        foreach ($artist_role->available_permissions as $permission) {
            $user->permissions()->attach($permission->id);
        }
        $user->requested_artist_account = 1;
        $user->save();
        $artist->user_id = $user->id;
        $artist->save();
        \DB::table('notifications')->where('id', $request->notification_id)->delete();

        //notify artist that his request was approved
        $from = (object)[
            'name' => auth()->user()->name,
            'avatar' => FileManager::asset_path(auth()->user()->avatar),
            'role' => auth()->user()->is_admin ? (__('CEO of') . ' ' . Setting::get('appName')) : __('Admin')
        ];
        $artist->notify(new Message(__('Artist account approved'), '<p></p><h2>Congratulation!</h2><p>your artist account request has been approved. You can access your artist panel from your user menu on the navbar.</p><p></p>', true, $from));
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Reject the user request: asking for an the artist account.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function rejectArtistAccountRequest(Request $request)
    {
        $notification = \DB::table('notifications')->where('id', $request->notification_id)->first();
        $notification_data = json_decode($notification->data);
        $artist = \App\Artist::find($notification_data->artist->id);
        $user =  \App\User::find($notification_data->user->id);
        $user->requested_artist_account = 1;
        $artist->delete();
        \DB::table('notifications')->where('id', $request->notification_id)->delete();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
