<div class="d-flex flex-column flex-md-row">

    <div class="mt-1">
        <span data-toggle="tooltip" data-placement="bottom" title="{{ __('Add files') }}."
            class="h-pill h-pill-primary file-upload-button {{ !GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks') ? 'disabled' : '' }}">
            @include('elements.icon', [
                'icon' => 'document-outline',
                'variant' => 'medium',
                'centered' => true,
                'classes' => 'mr-1',
            ])
        </span>
    </div>

</div>


{{--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>


<fieldset>
    <div class="form-group">
        <label for="document">Documents</label>
        <div class="needsclick dropzone" id="document-dropzone">
        </div>
    </div>
</fieldset>

<script type="text/javascript">
    var uploadedDocumentMap = {};
    Dropzone.options.documentDropzone = {
        url: '/echo/html/',
        maxFilesize: 10, // MB
        addRemoveLinks: true,
        success: function(file, response) {
            $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name
        },
        acceptedFiles: '.jpg, .gif, .mp3', //file extension or MIME Type to accept uploading
        removedfile: function(file) {
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined') {
                name = file.file_name
            } else {
                name = uploadedDocumentMap[file.name]
            }
            $('form').find('input[name="document[]"][value="' + name + '"]').remove()
        },
        init: function(e) {
            //dropbox initialization done!

        }


    }
</script>  --}}
