<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Message extends Model
{
    use HasFactory, NodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'type', 'project_id'];

    public function scopeSearch($query, $searchWord)
    {
        if( !empty( $searchWord ) ){

            return $query->where('content', 'like', '%'.$searchWord.'%');

        }

        return $query;
    }

    /**
     * Get the project associated with the message.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscribers that this message was sent to
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_messages')
                    ->withTimestamps();
    }

}
