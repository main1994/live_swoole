<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
# 软删除字段deleted_at
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 定义关联的表名，不定义的话默认此模型关联的表为 模型名s (users)
    protected $table = 'user';

    /* ------字段相关------*/
    # 定义主键字段名，默认是id
    protected $primaryKey = 'id';
    # 定义字段白名单，允许操作表中的哪些字段
    protected $fillable = ['phone'];
    # 定义字段黑名单，不允许操作表中哪些字段
    protected $guarded = [];
    // 1、使用model::create([])等方法直接对orm对象操作使，必须定义$guarded或者$fillable
    // 2、使用$m = new model();然后$m->save()的方式不需要定义

    # 定义隐藏的字段
    protected $hidden = [];

    /* ------时间管理------*/
    # 定义是否默认维护时间，默认是true.改为false，则以下时间相关设定无效
    public $timestamps = true;

    # 定义数据行创建时间和修改时间的字段名称。默认created_at,updated_at,没有设null
    const CREATED_AT = 'created';
    const UPDATED_AT = null;

    # 此属性决定插入和取出数据库的格式，默认datetime格式，'U'是int(10)
    protected $dateFormat = 'U';
    // 如果不设置,无论存储格式是datetime还是int，则取出时是Carbon对象
    // Carbon对象返给前端时自动变为datetime字符串，后端谨慎处理

    # 如果数据库存的是datetime或者没定义$dateFormat，又想取出的时候是int...
    public function getDates()
    {
        return ['created_at'];
    }
    //这里只能是created_at，updated_at，不是你自己的字段

    protected $dates = ['deleted_at'];
    # 查询数据时会自动排除掉deleted_at非null的数据，若向带上使用User::withTrashed()
    # 删除某条数据时使用$user->delete()然后save()即可。deleted_at由null变为时间.

    /* ------序列化------*/
    # toArray和toJson就是序列化方法，都是递归的。

    # 把ORM查询的数据自动转换。例如把int转boolean，时间戳转时间，json转成数组等。
    protected $casts = [
        'created'   => 'date:Y-m-d',
        'updated'   => 'datetime:Y-m-d H:i',
        'jsonData'  => 'array',
        'intSwitch' => 'boolean'
    ];
    // 关于这个时间的序列化，当作为接口返回的时候有效，如果直接直接页面渲染无效.
    // 时间序列化，当$timestamp为true时，无论是update还是new的方式插入新数据，都要是datetime
    // 注意取出的jsonData即是数组，而插入jsonData时也得是数组，不要json_encode


    /* ------自定义数据------*/
    # 自定义数据返回再如果，你想拿出来就是'2018/08/08 12:00'这种自定义格式,好比'sa_time'
    public function getSaTimeAttribute($value)
    {
        return date('Y/m/d H:i', $value);
    }
    // 注意方法名是根据字段来的
    // getxxAttribute 在获取xx属性的时候  $obj->xx 会转化  
    // setxxAttribute 在设置(sql: insert update) 的时候 会将$obj->xx = 'value'的时候, 操作数据库之前 自动转化一下
    // 自定义数据插入，存入之前的改变
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtolower($value);
    }

    // laravel 增加不存在数据库的字段
    //有时候您可能想要增加不存在数据库字段的属性数据。这时候只要定义一个获取器即可：
    public function getIsAdminAttribute()
    {
        return $this->attributes['admin'];
    }
    // 定义好获取器之后，再把对应的属性名称加到模型里的 appends 属性：
    protected $appends = ['is_admin'];
}
