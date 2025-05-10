<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Facades\Library;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;

class LibraryUploadRequest extends FormRequest
{
    /**
     * @return array<string, string|string[]>
     */
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
            'fileAlreadyExists' => fn (Validator $validator) => $this->checkFileExists($validator),
        ];
    }

    public function checkFileExists(Validator $validator): void
    {
        $filename = basename($validator->getValue('fileName'));
        $filename = str_replace('..', '', $filename);
        $folder = Library::getFolderByFilename(str_replace('.zip', '', $filename));

        $this->request->set('normalized_filename', $filename);
        $this->request->set('normalized_folder', $folder);

        if (! RequestFacade::boolean('overwrite') && RequestFacade::boolean('isFirst')) {
            if (Storage::disk('library')->exists($folder.'/'.$filename)) {
                $validator->errors()->add('fileAlreadyExists', 'File already exists');
            }

            if (
                \in_array($validator->getValue('fileType'), ['application/zip', 'application/x-zip-compressed'], true) &&
                Storage::disk('library')->exists($folder)
            ) {
                $validator->errors()->add('fileAlreadyExists', 'Folder already exists');
            }
        }
    }
}
