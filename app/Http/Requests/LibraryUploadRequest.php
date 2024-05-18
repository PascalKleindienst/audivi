<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;

class LibraryUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'chunk' => 'required',
            'fileName' => 'required',
            'fileType' => ['required', 'in:application/zip,application/x-zip-compressed,audio/mpeg'],
            'normalized_filename' => 'string',
            'normalized_folder' => 'string',
        ];
    }

    /**
     * @return array{fileAlreadyExists: Closure}
     */
    public function after(): array
    {
        return [
            'fileAlreadyExists' => function (Validator $validator) {
                $filename = basename($validator->getValue('fileName'));
                $filename = str_replace('..', '', $filename);
                $folder = str_replace('.zip', '', $filename);

                $this->request->set('normalized_filename', $filename);
                $this->request->set('normalized_folder', $folder);

                if (RequestFacade::boolean('isFirst') && ! RequestFacade::boolean('overwrite') && Storage::disk('library')->exists($filename)) {
                    $validator->errors()->add('fileAlreadyExists', 'File already exists');
                }

                if ($validator->getValue('fileType') === 'application/zip' || $validator->getValue('fileType') === 'application/x-zip-compressed') {
                    if (RequestFacade::boolean('isFirst') && ! RequestFacade::boolean('overwrite') && Storage::disk('library')->exists($folder)) {
                        $validator->errors()->add('fileAlreadyExists', 'Folder already exists');
                    }
                }
            },
        ];
    }
}
