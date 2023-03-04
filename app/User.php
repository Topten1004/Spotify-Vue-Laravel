<?php

namespace App;

use App\Http\Resources\AlbumResource;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\RadioStationResource;
use App\Http\Resources\SongResource;
use App\Http\Resources\Spotify\AlbumResource as SpotifyAlbumResource;
use App\Http\Resources\Spotify\SongResource as SpotifySongResource;
use Spotify;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * the attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'lang', 'email', 'password', 'avatar', 'hide_activity', 'available_disk_space', 'requested_artist_account', 'is_admin', 'stripe_id', 'card_brand', 'last_four'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];
    public function artist()
    {
        return $this->hasOne('App\Artist');
    }
    public function playlists()
    {
        return $this->hasMany('App\Playlist');
    }
    public function songs()
    {
        return $this->hasMany('App\Song');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
    public function permissions()
    {
        return $this->belongsToMany('App\Permission', 'user_permission');
    }
    public function plays()
    {
        return $this->hasMany('App\Play', 'user_id');
    }
    public function friends()
    {
        return $this->hasMany('App\Friendship');
    }
    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }
    public function active_subscription()
    {
        return $this->subscriptions()->where('status', 'active');
    }
    public function badge()
    {
        if( $sub =  $this->active_subscription()->first()) {
            if( $badge = $sub->plan->badge ) {
                return $badge;
            }
            return null;
        }
        return null;
    }
    public function plan()
    {
        if( $sub =  $this->active_subscription()->first()) {
            return $sub->plan;
        }
        return null;
    }
    public function messages()
    {
        return $this->hasMany('App\Chat');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function purchases()
    {
        return $this->hasMany(Sale::class);
    }
    public function followed_playlists()
    {
        return PlaylistResource::collection(\DB::table('follows')->select(\DB::raw('followed_id as followed_id'), \DB::raw('follows.user_id as follower_id'))->where('follows.user_id', $this->id)->where('followed_type', 'playlist')->join('playlists', 'playlists.id', '=', 'follows.user_id')->get());
    }
    public function likes($type)
    {
        $collection = new Collection();
        $localIds = 
            \DB::table('likes')
                ->select(\DB::raw('content_id as id'))
                ->where('user_id', $this->id)
                ->where('content_type', $type)
                ->where('content_source', 'local')
                ->get()->pluck('id')->toArray();

        if( count($localIds) ) {
            if( $type == 'album' ) {
                $local_likes = AlbumResource::collection(collect(Album::whereIn('id', $localIds)->get()));
            } else if ( $type === 'song' ) {
                $local_likes = SongResource::collection(collect(Song::whereIn('id', $localIds)->get()));
            }else if ( $type === 'radio-station' ) {
                $local_likes = RadioStationResource::collection(collect(RadioStation::whereIn('id', $localIds)->get()));
            }
            $collection = $collection->toBase()->merge($local_likes);
        }

        $spotifyIds = 
            \DB::table('likes')
                ->select(\DB::raw('content_id as id'))
                ->where('user_id', $this->id)
                ->where('content_type', $type)
                ->where('content_source', 'spotify')
                ->get()->pluck('id')->toArray();
        if( count($spotifyIds) ) {
            if( $type == 'album' ) {
                $spotify_likes = SpotifyAlbumResource::collection(collect(((object) Spotify::albums($spotifyIds)->get()['albums'])));
            } else if ( $type === 'song' ) {
                $spotify_likes = SpotifySongResource::collection(collect(((object) Spotify::tracks($spotifyIds)->get()['tracks'])));
            }
            $collection = $collection->toBase()->merge($spotify_likes);
        }
        return $collection;
    }
    public function is($roleName)
    {
        $plan = null;
        if ($sub = $this->active_subscription()->first()) {
            $plan = $sub->plan;
        }

        if ($plan) {
            $user_roles = $this->roles()->get()->toArray();
            $plan_roles = $plan->roles()->get()->toArray();
            $roles =  array_unique(array_merge($plan_roles, $user_roles), SORT_REGULAR);
        } else {
            $roles =  $this->roles()->get();
        }

        foreach ($roles as $role) {
            if ($role['name'] == $roleName) {
                return true;
            }
        }
        return false;
    }
    /**
     * Checks if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->is('admin') || $this->is_admin;
    }
    /**
     * Send the password reset notification.
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MailResetPasswordNotification($token));
    }

    /**
     * Returns the used disk space by the user.
     * @return string
     */
    public function used_disk_space()
    {

        return $this->songs()->where('uploaded_by', '!=', 'admin')->sum('file_size');
    }

    /**
     * What should happen on some CRUD operations.
     * @param string $token
     */
    public static function boot()
    {
        parent::boot();
        // attach the roles to a user when he is created.
        self::created(function ($model) {
            $model->roles()->attach(3);
            foreach (Role::find(3)->current_permissions as $permission) {
                $model->permissions()->attach($permission->id);
            }
            if (!$model->is_admin && Setting::get('enable_subscription')) {
                // subscribe to the first free plan ( if exists )
                if ($plan = Plan::where('free', 1)->first()) {
                    Subscription::create([
                        'status' => 'active',
                        'plan_id' => $plan->id,
                        'user_id' => $model->id,
                        'gateway' => 'local',
                        // 'renews_at' => $request->renews_at 
                    ]);
                }
            }
        });
        self::deleting(function ($model) {
            // delete the user avatar image after deletion
            \App\Helpers\FileManager::delete($model->avatar);
            // detach the user permissions after deletion
            foreach ($model->permissions as $permission) {
                $model->permissions()->detach($permission->id);
            }
            // detach the user roles after deletion
            foreach ($model->roles as $role) {
                $model->roles()->detach($role->id);
            }
            // detach the user notifications after deletion
            foreach ($model->notifications as $notification) {
                $model->notifications()->detach($notification->id);
            }
            // delete the user data after deletion
            $model->messages()->delete();
            $model->friends()->delete();
            $model->songs()->delete();
            $model->playlists()->delete();
        });
    }
}
