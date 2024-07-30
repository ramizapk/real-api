<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'notifiable_id', 'notifiable_type', 'data', 'read_at'];

    /**
     * Get the owning notifiable model.
     */

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }
    public function notifiable()
    {
        return $this->morphTo();
    }

    // طريقة لتعيين الإشعار كـ "مقروء"
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    // طريقة لتعيين الإشعار كـ "غير مقروء"
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }



    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }
}
