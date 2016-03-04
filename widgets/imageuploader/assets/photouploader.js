

function refresh_attachments(files, hidden_field_id)
{
    files.forEach(function (file) {
        window.files_list.push({hash: $(file).data('hash'), status: file.status, fileId: $(file).data('fileId')});
    });
    $('#' + hidden_field_id).val(JSON.stringify(window.files_list));
}