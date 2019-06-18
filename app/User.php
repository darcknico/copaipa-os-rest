<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Eloquence, Mappable;

    use Eloquence, Mappable;

    protected $table ='viewapp_afiliadosobrasocial';
    protected $primaryKey = 'nro_afiliado';
    public $incrementing = false;
    public $timestamps = false;

    protected $hidden = [
        'clave',
        'nro_afiliado',
    ];

    protected $maps = [
        'id' => 'nro_afiliado',
    ];

    protected $appends = [
        'id',
        'beneficiarios',
    ];

    public function getAuthPassword()
    {
        return $this->clave;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function getBeneficiariosAttribute(){
        $todo = DB::select('SELECT os_beneficiarios(?) AS beneficiarios',[
            $this['nro_afiliado']
        ]);
        return $todo[0]->beneficiarios??'';
    }
}
