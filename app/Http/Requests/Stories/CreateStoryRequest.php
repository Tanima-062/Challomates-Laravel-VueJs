<?php

namespace App\Http\Requests\Stories;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sales_partner_id'          =>  ['required', 'exists:sales_partners,id'],
            'check_in_id'               =>  ['required',],
            // 'mobile_app_user_id'        =>  ['required', 'exists:mobile_app_users,id'],
            'title'                     =>  ['nullable', 'max:150'],
            'media'                     =>  ['required','array', 'min:1', 'max:10'],
            'media.*'                   =>  ['required', 'mimes:png,jpg,heic,mp4,ogx,oga,ogv,ogg,webm,mov,avi,mkv,flv,wmv,mpg,mpeg,qt,m4v,m4p,mp2,mpe,mpv'],
            // 'media.*'                   =>  ['required', 'mimetypes:image/heic,image/heif,image/jpeg,image/png,video/webm,video/x-f4v,video/x-fli,video/x-flv,video/x-m4v,video/x-matroska,video/x-mng,video/x-ms-asf,video/x-ms-vob,video/x-ms-wm,video/x-ms-wmv,video/x-ms-wmx,video/x-ms-wvx,video/x-msvideo,video/x-sgi-movie,video/x-smv, video/3gpp2,  video/mp4, application/x-mpegURL,video/3gpp,video/quicktime,video/x-msvideo'],
            'tagged'                    =>  ['nullable', 'string']
            // 'tagged'                    =>  ['nullable','array'],
            // 'tagged.*'                    =>  ['nullable', 'exists:mobile_app_users,id']
        ];
    }

    public function messages()
    {
        return [
            'media.*.mimetypes' =>  'The media.0 must be a file of type: png, jpg, heic, mp4, ogx, oga, ogv, ogg, webm, mov, avi, mkv, flv, wmv, mpg, mpeg, qt, m4v, m4p, mp2, mpe, mpv.'
        ];
    }
}
