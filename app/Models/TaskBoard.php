<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
class TaskBoard extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;
    public $table = 'task_boards';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function taskBoardTasks()
    {
        return $this->hasMany(Task::class, 'task_board_id', 'id');
    }

    public function usersOnBoard()
    {
        return User::select('users.*')
            ->join('task_user', 'users.id', '=', 'task_user.user_id')
            ->join('tasks', 'tasks.id', '=', 'task_user.task_id')
            ->where('tasks.task_board_id', $this->id)
            ->distinct();
    }
}
