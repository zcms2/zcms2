{% extends "../../index.volt" %}
{% block content %}
    {% set status = '{{statusText}}' %}
    <div class="content">
        <form id="uploadFile" action="{{ _baseUri }}/admin/media/manager/uploadImage/" class="dropzone" enctype="multipart/form-data"></form>
    </div>
    <script src="{{ _baseUri }}/plugins/dropzonejs/dropzone.js"></script>
    <link rel="stylesheet" href="{{ _baseUri }}/plugins/dropzonejs/dropzone.css">

    <script>
        var errorUpload = [];
        var imageCount = 0;
        Dropzone.options.uploadFile = {
            paramName: "file",
            maxFileSize: {{ max_file_upload }}, // MB,
            dictResponseError: '{{ status }}',
            dictDefaultMessage: 'Drop or click to upload files!<br /><span style="font-size: 16px">Max file upload size {{ max_file_upload }}Mb</span>',
            success: function (file, response, e) {
                if (file.previewElement) {
                    return file.previewElement.classList.add("dz-success");
                }
                //console.log(file.type);
            }
        };
    </script>
{% endblock %}
