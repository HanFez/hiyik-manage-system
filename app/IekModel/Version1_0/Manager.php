<?php

namespace App\IekModel\Version1_0;



use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
class Manager extends IekModel implements Authenticatable {

    const SUPER_MAN = 2222;
    const MARK = [')', '!', '@', '#', '$', '%', '^', '&', '*', '('];
    protected $table='tblManagers';
    protected $fillable = ['id', 'password'];
    protected $hidden = [];
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function employee() {
        if($this->id != self::SUPER_MAN) {
            return $this->belongsTo(self::$NAME_SPACE . '\Employee', 'id', 'id');
        } else {
            return null;
        }
    }
    public static function initialSuperMan() {
        $m = self::where('id', self::SUPER_MAN)->first();
        $password = self::superManPassword();
        if(is_null($m)) {
            $m = new self;
            $m->id = self::SUPER_MAN;
            $m->password = $password;
        } else {
            $m->password = $password;
        }
        $m->save();
    }

    public static function superManPassword() {
        $time = Carbon::now();
        $password = sprintf("%d%02d%02d", $time->year, $time->month, $time->day);
        $day = $time->day;
        while($day >= 10) {
            $day = $day - 10;
        }
        $password = $password.self::MARK[$day];
        return Hash::make($password);
    }

    public function getAuthIdentifier() {
        return $this->id;
    }

    public function getAuthIdentifierName() {
        if($this->id == self::SUPER_MAN) {
            return $this->id;
        } else {
            $manager = $this->with('employee')->get();
            if(!is_null($manager->employee)) {
                return $manager->employee->name;
            } else {
                return null;
            }
        }
    }

    public static function initialSuperRole($name){
        $roleId = self::roles();
        $mr = [self::MANAGER_ID => $name,self::ROLE_ID => $roleId];
        $relat = ManagerRole::where(IekModel::MANAGER_ID,$name)->count();
        if($relat === 0){
            ManagerRole::insert($mr);
        }
    }

    public static function roles(){
        $roles = Role::where(IekModel::NAME,'super')->value('id');
        return $roles;
    }

    public function getRememberTokenName() {
        return 'remember_token';
    }

    public function getRememberToken() {
        return $this->remember_token;
    }

    public function setRememberToken($value) {
        $this->remember_token = $value;
    }

    public function getAuthPassword() {
        return $this->password;
    }

    public static function checkManager($id){
        $count = self::where(self::ID,$id)
            ->where(IekModel::CONDITION)
            ->count();
        return $count == 0 ? false : true;
    }

    public function managerRole(){
        return $this->hasMany(self::$NAME_SPACE.'\ManagerRole',IekModel::MANAGER_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }
}
