<?php

namespace App\Http\Controllers;

use App\Exceptions\FEException;
use App\File;
use App\Language;
use App\Setting;
use Barryvdh\TranslationManager\Models\Translation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{

    public function index()
    {
        return Language::all();
    }
    public function indexUser()
    {
        return Language::where('status', 1)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:languages',
            'locale' => 'required|string|unique:languages',
        ]);
        $language = Language::create([
            'name' => $request->name,
            'locale' => $request->locale,
            'status' => $request->status,
            'flag'   => strtolower($request->name) 
        ]);

        // populate translations
        $similar_translations = Translation::where('locale', $request->similar)->get();
        foreach ($similar_translations as $similar_translation) {
                $translation = Translation::firstOrNew([
                    'locale' => $request->locale,
                    'group' => '_json',
                    'key' => $similar_translation->key,
                ]);
                $translation->value = $similar_translation->value;
                $translation->save();
        }

        if( $request->isDefault ) {
            self::makeDefault($language);
        }

        Artisan::call('translations:export');

        return $language;
    }
    public function activate($id)
    {
        $lang = Language::find($id);
        try {
            ini_set('max_execution_time', 400);
            copy(base_path('resources/lang/backup/' . $lang->locale . '.json'), base_path('resources/lang/' . $lang->locale . '.json'));
            Artisan::call('translations:import');
            $lang->status = 1;
            return $lang->save();
        } catch(Exception $e) {
            throw new FEException('Failed to activate the language.', $e->getMessage(), 500);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $language = Language::find($id);

        if( $request->isDefault ) {
            self::makeDefault($language);
        }

        $language->name = $request->name; 
        $language->status = $request->status; 
        $language->save();

        return $language;
    }

    public function messages($locale)
    {
        return Translation::where('locale', $locale)->orderBy('created_at', 'asc')->get();
    }
    public function appMessages($locale)
    {
        $en_messages = Cache::get('messages-en', Translation::where('locale', 'en')->get()->pluck('value', 'key'));
        if( $locale !== 'en' ) {
            $locale_messages = Cache::get('messages-' . $locale, Translation::where('locale', $locale)->get()->pluck('value', 'key'));
            $messages =  response()->json([
                "en" => $en_messages,
                $locale => $locale_messages
            ]);
        } else {
            $messages =  response()->json([
                "en" => $en_messages
            ]);
        }

        return $messages;
    }
    public function saveMessages(Request $request, $locale)
    {
        // save messages
        $newMessages = $request->messages;
        foreach ($newMessages as $message) {
            if( $message['status'] == -1 && isSet($message['id']) ) {
                Translation::find($message['id'])->delete();
            } else {
                $translation = Translation::firstOrNew([
                    'locale' => $message['locale'],
                    'group' => $message['group'],
                    'key' => $message['key'],
                ]);
                $translation->value = (string) $message['value'] ?: null;
                $translation->save();
            }
        }
        Artisan::call('translations:export');

        $new_messages = Translation::where('locale', $locale)->orderBy('created_at')->get(); 
        Cache::put('messages' . $locale, $new_messages);
        return $new_messages;
    }

    public function deleteMessages($locale)
    {
        return Translation::where('locale', $locale)->delete();
    }

    public function destroy($id)
    {
        $language = Language::find($id);
        $this->deleteMessages($language->locale);
        // replace the default locale to 'en' if the deleted one was the default
        if( Setting::get('locale') == $language->locale ) {
            Setting::set('locale', 'en');
            app()->setlocale('en');
            $language->delete();
            return Language::where('locale', 'en')->first();
        } 
        
        // delete file //

        $language->delete();
        return Language::where('locale', Setting::get('locale'))->first();
    }

    // static

    static function makeDefault($lang) {

        $language = Language::find($lang->id);

        Language::where('isDefault', 1)
            ->where('locale', '!=', $language->locale)
            ->update(['isDefault' => 0]);

        $language->isDefault = 1;
        $language->save();
        
        app()->setlocale($language->locale);
        auth()->user()->lang = $language->locale;
        Setting::set('locale', $language->locale);
        auth()->user()->save();
    }
}
