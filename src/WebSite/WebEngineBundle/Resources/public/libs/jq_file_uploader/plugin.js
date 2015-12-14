/*
 data structure
 date =
 files: {
 [
 file:{
 label: 'label',
 img: 'binary date',
 link: 'link to file',
 delete: 'link to delete'
 }
 ]
 }
 state: 'ok' or 'failed',
 error: 'error message'
 */
var jq_file_uploader = function (options) {
    var _options = {
        useDragAndDrop: true,
        maxSize: 10000,
        useFileInput: true,
        showFileList: false,
        fileLabelLength: 11,
        urlUpload: '/',
        fileList: [],
        onFileListNodeCreate: function(event, node, filess){},
        onFileListLoadComplete: function(event, xhr){}
    }

    var view = null;
    var file = null;
    var useHTML5 = true;

    _construct(options);
    function _construct(options) {
        for (var key in options) {
            _options[key] = options[key];
        }
        _checkSupportedHTML5();
        if(useHTML5)
        {

            _createViewHtml5();
        }
        else
        {
            _createViewIframe();
        }

        _addEventsToView();
    }

    function _checkSupportedHTML5()
    {
        if (typeof(window.FileReader) == 'undefined') {
            _options.useDragAndDrop = false;
            _options.useFileInput = true;
        }

        var input = document.createElement('input');
        input.setAttribute('type', 'file');

        if(typeof(input.files) == 'undefined')
        {
            useHTML5 = false;
        }

        delete input;
    }

    function _createViewIframe()
    {
        _baseCreateView();
        view.iFrame = $('<iFrame>').hide();
        view.append(view.iFrame);
        view.caption = 'Выберите файл';
        view.setCaption();
    }

    function _createViewHtml5()
    {
        _baseCreateView();
        if(_options.useDragAndDrop)
        {
            view.caption = 'Выберите файл или перетащите его';
        }
        else
        {
            view.caption = 'Выберите файл';
        }
        view.setCaption();
    }

    function _byteToK(size)
    {
        var size_ = '';
        if(size < 1000)
        {
            size_ = size +' byte';
        }
        if(size >= 1000)
        {
            size_ = Math.round(size/1000) + ' Kb';
        }

        if(size >= 1000000)
        {
            size_ = Math.round(size/1000000) + ' Mb';
        }

        if(size >= 1000000000)
        {
            size_ = Math.round(size/1000000000) + ' Gb';
        }

        return size_;
    }

    function _baseCreateView()
    {
        var idMain = 'jq_file_uploader' + ($(document).find('.jq_file_uploader').length + 1);
        view = $($('<div>').addClass('jq_file_uploader').attr('id', idMain));
        view.append($('<form style="display: none;">').append($('<input type="file" name="file">')).append($('<button type="submit">')));
        view.append(($('<ul class="buttons">').append($('<li>').append('<div class="choice_file">Выберите</div>'))
            .append($('<li>').append('<button class="button_upload_file">Загрузить</button>'))));

        view.choiceFile = view.find('.choice_file');
        view.choiceFile.parent().parent().prepend($('<li>').append($('<div class="max_size">').text('Максимальный размер файла ' + _byteToK(_options.maxSize))));
        view.buttonSend = view.find('.button_upload_file');
        view.buttonSend.hide();

        view.progressBar = $($('<div class="progress_container"></div>').append($('<div class="progress_value">').width(0)));
        view.progressBar.progressValue = view.progressBar.find('.progress_value');
        view.progressBar.progressValue.height(view.progressBar.height());
        view.progressBar.hide();
        view.append(view.progressBar);

        view.fileList = $($('<ul class="file_list">'));
        view.append(view.fileList);
        if(!_options.showFileList)
        {
            view.fileList.hide();
        }

        view.errorMess = $($('<div class="error_message" style="position: absolute">'));
        view.errorMess.hide();
        view.append(view.errorMess);

        view.setFileLabel = function(name, size)
        {
            size = _byteToK(size);
            var label = name + ' (' + size + ')';
            view.choiceFile.text(label);
            view.buttonSend.show();
        }

        view.caption = '';
        view.setCaption = function(){
            view.choiceFile.text(view.caption);
        }

        if(_options.showFileList)
        {
            _buildFileList(_options.fileList);
        }
    }

    function _addEventsToView()
    {
        var choiceFileArea = view.choiceFile;

        choiceFileArea.click(function(){
            view.find('input[type=file]').click();
        });

        view.buttonSend.click(function(e){
            e.preventDefault();
            if(useHTML5)
            {
                _uploadWithHtml5();
            }
            else
            {
                _uploadFromIframe();
            }
        });

        view.errorMess.click(function(){
            $(this).fadeOut(400);
        });

        if(useHTML5)
        {
            view.find('input[type=file]').on('change', function(e){
                e.preventDefault();

                if($(this).get(0).files.length > 0)
                {
                    file = $(this).get(0).files[0];
                    if(file.size > _options.maxSize)
                    {
                        _errorMessageUp('Размер Файла ' +file.name + ' больше максимально разрешенного');
                        file = null;
                        return false;
                    }
                    view.setFileLabel(file.name, file.size);
                }
                return false;
            });
        }
        if(_options.useDragAndDrop)
        {
            $(document).on('dragover', function(e){
                e.preventDefault();
                $(view.choiceFile).addClass('drag_hover');
                return false;
            });
            $(document).on('dragleave', function(e){
                e.preventDefault();
                $(view.choiceFile).removeClass('drag_hover');
                return false;
            });
            $(document).on('drop', function(e){
                e.preventDefault();
                $(view.choiceFile).removeClass('drag_hover');
                file = e.originalEvent.dataTransfer.files[0];
                if(file.size > _options.maxSize)
                {
                    _errorMessageUp('Размер Файла ' +file.name + ' больше максимально разрешенного');
                    file = null;
                    return false;
                }
                view.setFileLabel(file.name, file.size);
                return false;
            });
        }
    }

    function _uploadFromIframe()
    {}

    function _uploadWithHtml5()
    {
        var xhr =  new XMLHttpRequest();
        var formData = new FormData();
        formData.append('file', file);
        xhr.upload.addEventListener('progress', progressShow, false);
        xhr.onreadystatechange = _onCompleteUpload;
        xhr.open('POST',_options.urlUpload, true);
        xhr.send(formData);
        view.buttonSend.hide();
        view.progressBar.show();

        function progressShow(e)
        {
            var percent = parseInt(e.loaded / e.total * 100);
            if(percent >= 100)
            {
                percent = 100;
            }
            var absoluteValue = parseInt(percent * view.progressBar.width()/100);
            view.progressBar.progressValue.width(absoluteValue);
        }

        function _onCompleteUpload(e)
        {
            if(e.target.readyState == 4)
            {
                file = null;
                view.setCaption();
                view.progressBar.hide();
                if(e.target.status != '200')
                {
                    _errorMessageUp('Произошла ошибка ' + e.target.status + ' ' + e.target.statusText);
                    return;
                }

                var data = JSON.parse(e.target.responseText);
                if(data.state == 'failed')
                {
                    _errorMessageUp(data.error);
                    return;
                }

                if(_options.showFileList)
                {
                    if(typeof data.files === 'string')
                    {
                        data.files = JSON.parse(data.files);
                    }
                    _buildFileList(data.files);
                }
            }
        }

        delete xhr;
    }

    function _deleteFile(nodeA)
    {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', nodeA.attr('href'), true);
        xhr.onreadystatechange = _onCompleteUpload;
        xhr.send();

        function _onCompleteUpload(e)
        {
            if(e.target.readyState == 4)
            {
                if(e.target.status != '200')
                {
                    _errorMessageUp('Произошла ошибка ' + e.target.status + ' ' + e.target.statusText);
                    nodeA.removeClass('file_delete_process');
                    nodeA.addClass('file_delete_link');
                    nodeA.text('Удалить');
                    nodeA.attr('state', 0);
                    return;
                }
                var data = JSON.parse(e.target.responseText);
                if(data.state == 'ok')
                {
                    nodeA.parents('li').fadeOut(400);
                }
                else if(data.state == 'failed')
                {
                    nodeA.removeClass('file_delete_process');
                    nodeA.addClass('file_delete_link');
                    nodeA.text('Удалить');
                    nodeA.attr('state', 0);
                    _errorMessageUp(data.error);
                }
            }
        }

    }

    function _buildFileList(files)
    {
        if(files.length == 0)
        {
            return;
        }

        view.fileList.hide();
        view.fileList.empty();
        for(var i in files)
        {
            var liNode = $($('<li>'));
            var deleteTag = $('<a>').text('Удалить').addClass('file_delete_link');
            var file = files[i];
            var caption = file.label;
            if(file.img)
            {
                liNode.append($('<img>').attr('src', file.img));
            }
            if(file.label.length > _options.fileLabelLength)
            {
                caption = file.label.substring(0, _options.fileLabelLength - 3) + '...';
            }
            liNode.append($('<a target="_blank">').attr('href', file.link).text(caption).attr('title', file.label).addClass('file_link'));
            liNode.append(deleteTag);
            deleteTag.attr('state',0);
            deleteTag.attr('href', file.delete_);
            deleteTag.click(function(e){
                e.preventDefault();
                if($(this).attr('state') == 0)
                {
                    $(this).fadeOut(200);
                    $(this).attr('state',1);
                    $(this).after($('<a href="#">').text('Отмена').addClass('file_delete_cancel').click(function(e){
                        e.preventDefault();
                        $(this).prev('.file_delete_link').attr('state', 0);
                        $(this).remove();
                        return false;
                    }));
                    $(this).fadeIn(200);
                }
                else if($(this).attr('state') == 1)
                {
                    $(this).text('Файл удаляется');
                    $(this).next('.file_delete_cancel').remove();
                    $(this).attr('state',2);
                    $(this).removeClass('file_delete_link');
                    $(this).addClass('file_delete_process');
                    _deleteFile($(this));
                }
                return false;
            });
            liNode.on('jq_file_uploader.file_list_node_create', _options.onFileListNodeCreate);
            liNode.trigger('jq_file_uploader.file_list_node_create', [liNode, file]);
            view.fileList.append(liNode);
        }
        view.fileList.fadeIn(400);
    }

    function _errorMessageUp(message)
    {
        var top = view.offset().top + (view.height()/2 - view.errorMess.height());
        var left = view.offset().left + (view.width()/2 - view.errorMess.width());

        view.errorMess.css('top', top);
        view.errorMess.css('left', left);
        view.errorMess.text(message).fadeIn(400);
    }

    this.getView = function()
    {
        return view;
    }


}
