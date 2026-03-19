<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CompanySetting extends Model
{
	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */


	protected static function boot()
	{
		parent::boot();

		/*static::addGlobalScope('company', function (Builder $builder) {
			$builder->where('company_id', '=', auth()->user()->company_id ?? 0 );
		});*/
		/*static::creating(function($modal){
			$modal->company_id = auth()->user()->company_id;
		});*/
	}
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_settings';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'value', 'module'];
    public static $saveFolder = '/images/companies/';
	/**
	 * Danh sách [key => default value] - key dùng để lưu vào trường key trong bảng settings
	 * @var array
	 */
    public static $key = [
//        'is_send_notify_booking_tmp' => 1,
//	    'minutes_repeat_notify_booking_tmp' => 15,
        'send_birthday_messages' => 1
    ];
	public static $key_type = [
//		'is_send_notify_booking_tmp' => 'checkbox',
        'send_birthday_messages' => 'checkbox'
	];
	public static $key_validate = [
//		'is_send_notify_booking_tmp' => 'nullable|in:0,1',
//		'minutes_repeat_notify_booking_tmp' => 'required_if:is_send_notify_booking_tmp,1|min:0',
        'send_birthday_messages' => 'nullable|in:0,1',
	];
	public static function getValue($key){
		if(array_key_exists($key, self::$key)) {
			$version = CompanySetting::where( 'key', $key )->value( 'value' );
			if ( ! isset( $version ) ) {
				$version = self::$key[ $key ];
			}

			return $version;
		}
		dd('Key not exist! (Setting::getValue)');
	}

    public static function saveLogo($file){
        if(empty($file)) return;

        $fileName = time().'_'.$file->getClientOriginalName();
        $path = self::$saveFolder . $fileName;
        if(!\Storage::disk(config('filesystems.disks.public.visibility'))->has(self::$saveFolder)){
            \Storage::makeDirectory(config('filesystems.disks.public.visibility').self::$saveFolder);
        }
        \Image::make($file->getRealPath())->resize(100,100)->save(public_path('/storage/').$path);
        return  config('filesystems.disks.public.visibility').$path;
    }

    public static function profile(){
	    //Lấy thông tin công ty
	    $companyInitial = config('company_settings.company_key');
	    $arKeyConfig = array_column($companyInitial, 'key');
	    $company = CompanySetting::whereIn('key', $arKeyConfig)->pluck('value','key');
	    $companyProfile = [];
	    foreach (config('company_settings.company_key') as $setting){
		    $value = null;
		    if(!empty($company[$setting['key']])){
			    $value = $company[$setting['key']];
			    $indexConfig = array_search($setting['key'], $arKeyConfig);
			    if($companyInitial[$indexConfig]['type'] === 'file'){
				    $value = asset($value);
			    }
		    }
		    $companyProfile[$setting['key']] = $value;
	    }
	    return $companyProfile;
    }

}
