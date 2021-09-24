<?php

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

if (!function_exists('nano_id')) {
    /**
     * @return string
     *
     * @throws BindingResolutionException
     */
    function nano_id()
    {
        return app()->make('nanoid')->generateId();
    }
}

if (!function_exists('snowflake_id')) {
    /**
     * @return Snowflake
     *
     * @throws BindingResolutionException
     */
    function snowflake_id()
    {
        return app()->make('snowflake')->id();
    }
}
if (!function_exists('file_upload')) {
    /**
     * @param File|UploadedFile $file
     * @param string $path
     * @param array $options
     *
     * @return string
     */
    function file_upload($file, $path, $options = [])
    {
        $fileId = Storage::putFile($path, $file, $options);

        return str_replace($path . '/', '', $fileId);
    }
}

if (!function_exists('file_get_url')) {
    /**
     * @param string $fileId
     * @param null $path
     * @param bool $expiry
     * @return string
     */
    function file_get_url(?string $fileId, $path = null, $expiry = false)
    {
        if (is_null($fileId)) {
            return null;
        }

        if ($expiry) {
            try{
                return Storage::temporaryUrl($path . $fileId, Carbon::now()->addDay());
            }catch (RuntimeException $exception){
                return Storage::url($path . $fileId);
                //TODO BETTER HANDLING unsupported adapter method temporaryUrl
            }
        }

        return Storage::url($path . $fileId);
    }
}

if (!function_exists('file_get_temp_url')) {
    /**
     * @param string $fileId
     * @param null $path
     * @param bool $expiry
     * @return string
     */
    function file_get_temp_url(string $fileId, $path = null)
    {
        if (is_null($fileId)) {
            return '';
        }

        return Storage::temporaryUrl($path . $fileId, Carbon::now()->addDay());
    }
}

if (!function_exists('asset')) {
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('public_path')) {
    /**
     * Return the path to public dir
     * @param null $path
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

if (! function_exists('bcrypt')) {
    /**
     * Hash the given value against the bcrypt algorithm.
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     */
    function bcrypt($value, $options = [])
    {
        return app('hash')->driver('bcrypt')->make($value, $options);
    }
}

if (!function_exists('unix_timestamp')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function unix_timestamp($date)
    {
        return optional(Carbon::make($date))->timestamp;
    }
}
