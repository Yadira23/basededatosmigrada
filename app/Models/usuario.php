<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordUsuario;

class Usuario extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    public $timestamps = true;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $guard_name = 'web';

    protected $fillable = ['usuario_usr', 'nombre_usr', 'apellido_paterno', 'apellido_materno', 'email_usr', 'password', 'id_depen', 'estado_usr', 'telefono_usr', 'password'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getEmailAttribute()
    {
        return $this->email_usr;
    }

    public function setEmailAttribute($value)
    {
        $this->email_usr = $value;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_usr;
    }

    public function getAuthIdentifierName()
    {
        return 'email_usr';
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email_usr;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordUsuario($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bitacora()
    {
        return $this->hasOne('App\Models\Bitacora', 'id_usuario', 'id_usuario');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dependencia()
    {
        return $this->belongsTo('App\Models\Dependencia', 'id_depen', 'id_depen');
    }

    protected function getDefaultGuardName(): string
    {
        return 'web';
    }
}
