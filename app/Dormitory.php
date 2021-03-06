<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dormitory extends Model
{
    protected $fillable = [
        'name', 'description', 'status', 'pic'
    ];

    protected $appends = ['total_room', 'capacity', 'available', 'reserved'];

    protected $with = ['rooms'];

    public function rooms() {
        return $this->hasMany(DormitoryRoom::class);
    }

    public function getTotalRoomAttribute() {
        return $this->rooms->count();
    }

    public function getCapacityAttribute() {
        return DormitoryRoom::selectRaw('SUM(capacity) AS capacity')
            ->where('dormitory_id', $this->id)
            ->first()->capacity;
    }

    public function getReservedAttribute()
    {
        return DormitoryReservation::selectRaw('COUNT(dormitory_reservations.id) AS reserved')
            ->join('dormitory_rooms', 'dormitory_rooms.id', '=', 'dormitory_reservations.dormitory_room_id')
            ->join('dormitories', 'dormitories.id', '=', 'dormitory_rooms.dormitory_id')
            ->where('dormitory_rooms.dormitory_id', $this->id)
            ->whereRaw("((DATE(NOW()) BETWEEN check_in AND check_out AND is_checked_out = 0) OR is_checked_out = 0)")
            ->first()->reserved;
    }

    public function getAvailableAttribute()
    {
        return $this->capacity - $this->reserved;
    }

}
