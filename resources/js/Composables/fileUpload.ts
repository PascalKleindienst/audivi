import { computed, ComputedRef, reactive } from 'vue';
import { AxiosError, AxiosResponse } from 'axios';

export interface UploadState {
    /** List of files to be uploaded */
    files: FileUpload[];

    /** Boolean indicating if there is an upload in progress */
    uploading: boolean | ComputedRef<boolean>;

    /** Boolean indicating if there was an error during the upload */
    hasError: boolean | ComputedRef<boolean>;

    /** Number of files waiting to be uploaded */
    waiting: number | ComputedRef<number>;

    /** Number of files successfully uploaded */
    successfull: number | ComputedRef<number>;

    /** Number of files failed during the upload */
    failed: number | ComputedRef<number>;
}

export interface FileUpload {
    /** File to be uploaded */
    file: File;

    /** File size in human-readable format */
    fileSize: string;

    /** Current upload process in percent */
    process: number;

    /** Boolean indicating if the upload is complete */
    complete: boolean;

    /** Boolean indicating if the upload is in progress */
    uploading: boolean;

    /** Error message if the upload failed */
    error?: string;
}

/**
 * Format bytes as human-readable text.
 *
 * @param bytes Number of bytes.
 * @param si True to use metric (SI) units, aka powers of 1000. False to use
 *           binary (IEC), aka powers of 1024.
 * @param dp Number of decimal places to display.
 *
 * @return Formatted string.
 */
function humanFileSize(bytes: number, si = false, dp = 1) {
    const thresh = si ? 1000 : 1024;

    if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }

    const units = si ? ['kB', 'MB', 'GB'] : ['KiB', 'MiB', 'GiB'];
    let u = -1;
    const r = 10 ** dp;

    do {
        bytes /= thresh;
        ++u;
    } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);

    return bytes.toFixed(dp) + ' ' + units[u];
}

/**
 * Function to handle file uploads
 * @param uploadRoute - The route to upload the files
 * @param mimeTypes - The accepted mime types for file uploads
 * @returns An object with state, upload, reset, add, and remove methods
 */
export function useFileUpload(
    uploadRoute: string,
    mimeTypes: string[] = ['application/zip', 'application/x-zip-compressed', 'audio/mpeg']
) {
    // Define the state object
    const state: UploadState = reactive<UploadState>({
        files: [],
        uploading: computed<boolean>(() => state.files.filter((file: FileUpload) => file.uploading).length > 0),
        hasError: computed<boolean>(() => state.files.filter((file: FileUpload) => file.error).length > 0),
        waiting: computed<number>(() => state.files.length - Number(state.successfull) - Number(state.failed)),
        successfull: computed<number>(
            () => state.files.filter((file: FileUpload) => !file.error && file.complete).length
        ),
        failed: computed<number>(() => state.files.filter((file: FileUpload) => file.error).length)
    });

    /**
     * Reset the state
     */
    const reset = () => {
        state.files = [];
    };

    /**
     * Remove a file from the upload list
     */
    const remove = (file: FileUpload) => {
        state.files = state.files.filter((f: FileUpload) => f !== file);
    };

    /**
     * Add a number of files to the upload list
     */
    const add = (files: FileList) => {
        for (let i = 0; i < files.length; i++) {
            const file = files.item(i);
            if (!file || !mimeTypes.includes(file.type)) {
                continue;
            }

            state.files.push({
                file,
                fileSize: humanFileSize(file.size),
                process: 0,
                complete: false,
                uploading: false
            });
        }
    };

    /**
     * Uplooad a file to the server in chunks using FileReader
     * @param fileUpload - The file to be uploaded
     * @param overwriteFiles - Flag to indicate if the files should be overwritten
     */
    const upload = async (fileUpload: FileUpload, overwriteFiles = false) => {
        // Initialize FileReader and variables
        const reader = new FileReader();
        let offset = 0;
        let isFirst = true;
        const FILE_UPLOAD_CHUNK_SIZE = 1048576; // 1024 * 1024 = 1 MiB

        fileUpload.uploading = true;

        // Loop until file upload is complete
        while (!fileUpload.complete) {
            const nextChunk = offset + FILE_UPLOAD_CHUNK_SIZE + 1;
            const currentChunk = fileUpload.file?.slice(offset, nextChunk);

            try {
                // Wait for the chunk to be loaded
                fileUpload.complete = await new Promise((resolve) => {
                    // Register for the loaded event of the file reader
                    reader.onloadend = async (event: ProgressEvent<FileReader>) => {
                        if (event.target?.readyState !== FileReader.DONE) {
                            return;
                        }

                        const isFinal = nextChunk >= fileUpload.file?.size;

                        try {
                            // Send the chunk data to the server
                            const result: AxiosResponse<{ success: boolean; message?: string }> =
                                await window.axios.post(
                                    uploadRoute,
                                    {
                                        chunk: event.target?.result,
                                        fileName: fileUpload.file?.name,
                                        fileType: fileUpload.file?.type,
                                        fileSize: fileUpload.file?.size,
                                        isFirst: isFirst,
                                        isFinal: isFinal,
                                        overwrite: overwriteFiles
                                    },
                                    {
                                        headers: { 'content-type': 'application/x-www-form-urlencoded' }
                                    }
                                );

                            // Handle an error server response
                            if (!result || !result.data || !result.data.success) {
                                fileUpload.error = result.data.message;
                                return;
                            }

                            // advance the progress
                            const sizeDone = offset + FILE_UPLOAD_CHUNK_SIZE;
                            fileUpload.process = Math.min(100, Math.floor((sizeDone / fileUpload.file?.size) * 100));
                            resolve(isFinal);
                        } catch (err) {
                            if (err instanceof AxiosError) {
                                fileUpload.error = err.response?.data.message;
                                return;
                            }

                            if (err instanceof Error) {
                                fileUpload.error = err.message;
                            }
                        }
                    };

                    // We use readAsDataURL to prevent encoding problems. (This encodes the data into base64.)
                    // This will trigger the event registered above.
                    reader.readAsDataURL(currentChunk);
                });
            } catch (err) {
                if (err instanceof Error) {
                    fileUpload.error = err.message;
                }
                break;
            }

            isFirst = false;
            if (!fileUpload.complete) {
                offset = nextChunk;
            }
        }
    };

    return {
        state,
        upload,
        reset,
        add,
        remove
    };
}
