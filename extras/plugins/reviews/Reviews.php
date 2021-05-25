<?php

namespace extras\plugins\reviews;

use App\Helpers\DBTool;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Prologue\Alerts\Facades\Alert;

class Reviews
{
	/**
	 * @return string
	 */
	public static function getAdminMenu()
	{
		$out = '<li><a href="' . admin_url('reviews') . '"><i class="fa fa-commenting-o"></i> <span>' . trans('reviews::messages.Reviews') . '</span></a></li>';
		
		return $out;
	}
	
	/**
	 * @return array
	 */
	public static function getOptions()
	{
		$options = [];
		$options[] = (object)[
			'name'     => trans('reviews::messages.Reviews'),
			'url'      => admin_url('reviews'),
			'btnClass' => 'btn-primary',
			'iClass'   => 'fa fa-commenting-o',
		];
		$setting = Setting::active()->where('key', 'reviews')->first();
		if (!empty($setting)) {
			$options[] = (object)[
				'name'     => mb_ucfirst(trans('admin::messages.settings')),
				'url'      => admin_url('settings/' . $setting->id . '/edit'),
				'btnClass' => 'btn-info',
			];
		}
		
		return $options;
	}
	
	/**
	 * @return bool
	 */
	public static function isPreInstalled()
	{
		if (
			Schema::hasTable('reviews') &&
			Schema::hasColumn('posts', 'rating_cache') &&
			Schema::hasColumn('posts', 'rating_count')
		) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function installed()
	{
		$setting = Setting::active()->where('key', 'reviews')->first();
		
		if (
			!empty($setting) &&
			Schema::hasTable('reviews') &&
			Schema::hasColumn('posts', 'rating_cache') &&
			Schema::hasColumn('posts', 'rating_count')
		) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function install()
	{
		// Remove the plugin entry
		if (!self::isPreInstalled()) {
			self::uninstall();
		}
		
		try {
			// Perform the plugin SQL queries
			if (!self::isPreInstalled()) {
				$updateSqlFile = plugin_path('reviews', 'database/install.sql');
				if (file_exists($updateSqlFile)) {
					$sql = file_get_contents($updateSqlFile);
					$sql = str_replace('<<prefix>>', DB::getTablePrefix(), $sql);
					DB::unprepared($sql);
				}
			}
			
			// Create plugin setting
			DB::statement('ALTER TABLE ' . DBTool::table('settings') . ' AUTO_INCREMENT = 1;');
			$pluginSetting = [
				'key'         => 'reviews',
				'name'        => 'Reviews',
				//'value'     => null,
				'description' => 'Reviews System',
				'field'       => null,
				'parent_id'   => 0,
				'lft'         => 32,
				'rgt'         => 33,
				'depth'       => 1,
				'active'      => 1,
			];
			$setting = Setting::create($pluginSetting);
			if (empty($setting)) {
				return false;
			}
			
			return true;
		} catch (\Exception $e) {
			$msg = cleanAddSlashes($e->getMessage(), '"');
			Alert::error($msg)->flash();
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function uninstall()
	{
		try {
			// Remove plugin data
			$updateSqlFile = plugin_path('reviews', 'database/uninstall.sql');
			if (file_exists($updateSqlFile)) {
				$sql = file_get_contents($updateSqlFile);
				$sql = str_replace('<<prefix>>', DB::getTablePrefix(), $sql);
				DB::unprepared($sql);
			}
			
			// Remove the plugin setting
			$setting = Setting::where('key', 'reviews')->first();
			if (!empty($setting)) {
				$setting->delete();
			}
			
			return true;
		} catch (\Exception $e) {
			$msg = cleanAddSlashes($e->getMessage(), '"');
			Alert::error($msg)->flash();
		}
		
		return false;
	}
	
	/**
	 * @return string
	 */
	public static function getFieldData()
	{
		$value = '{"name":"guests_comments","label":"' . cleanAddSlashes(trans('reviews::messages.guests_comments_label')) . '","type":"checkbox","hint":"' . cleanAddSlashes(trans('reviews::messages.guests_comments_hint')) . '","wrapperAttributes":{"class":"form-group col-md-6"},"plugin":"reviews","disableTrans":"true"}';
		
		return $value;
	}
}
