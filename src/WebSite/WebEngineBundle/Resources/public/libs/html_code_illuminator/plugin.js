var html_illuminator = function(id, html, options){

    var divO = null;
    var textarea = null;
    var editWindow = null;
    var divView = null;
    var excludedTags = ['br', 'img', 'input'];
    var _options = {
        onApplyChanges: function (event, code) {
        }
    }

    _construct(id, html, options);
    function _construct(id, html, options){
        if (typeof id != 'string') {
            throw  new Error(id + ' is not string');
        }
        if (id.indexOf('#') != 0) {
            throw  new Error(id + ' is not identificator of DOM-element ');
        }

        for (
            var p in options) {
            _options[p] = options[p]
        }

        divO = $($(id)).addClass('html_illuminator_container');
        divO.on('hi.change_content', _options.onApplyChanges);

        if(html == undefined || html.length == 0)
        {
            html = '<div>Enter point</div>';
        }

        createView(html);
    }

    function createView(html){

        editWindow = $($('<div>')).addClass('html_illuminator_edit_window');
        var divEditWindow = $($('<div>'));
        divEditWindow.appendTo(editWindow);

        var divPanel = $($('<div>')).addClass('html_illuminator_control_panel');
        var buttonOk = $($('<button>')).text('Ok (ctrl+enter)').click(applyChange);
        buttonOk.appendTo(divPanel);
        divPanel.append($('<button>Cancel (esc)</button>').click(cancel));

        textarea = $($('<textarea contenteditable="true">')).addClass('html_illuminator_text');
        /*textarea.val = function(value){
            if(value == undefined)
            {
                return textarea.text();
            }
            else
            {
                textarea.text(value);
            }
        }*/
        textarea.keydown(function(e){
            if (e.ctrlKey && e.keyCode == 13)
            {
                applyChange(e);
            }

            if(e.keyCode == 27)
            {
                editWindow.hide();
            }

            if(e.keyCode == 9)
            {
                e.preventDefault();
                if (document.selection)
                {
                    document.selection.createRange().text = '    ';
                }
                else if(this.selectionStart || this.selectionEnd)
                {
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var value = '   ';
                    this.value = this.value.substring(0, startPos) + value + this.value.substring(endPos, this.value.length);
                    this.selectionStart = startPos + value.length;
                    this.selectionEnd = endPos + value.length;
                }
            }
        });
        textarea.appendTo(divEditWindow);

        editWindow.textarea = textarea;
        editWindow.appendTo(divO);

        divPanel.appendTo(divEditWindow);

        var divModePanel = $($('<div>'));
        $('<a href="#">').text('code').click(function(e){
            e.preventDefault();
            $(this).next().removeClass('active');
            $(this).addClass('active');
            var content = $(divO.code.replace('<!--', '<comment>').replace('-->', '</comment>'));
            content = wrap(content);
            divView.empty();
            content.appendTo(divView);

        }).appendTo(divModePanel).addClass('active');
        $('<a href="#">').text('preview').click(function(e){
            e.preventDefault();
            $(this).addClass('active');
            $(this).prev().removeClass('active');
            divO.code = divView.text();
            divView.html(divO.code);
        }).appendTo(divModePanel);

        divModePanel.appendTo(divO);

        divView = $($('<div>')).addClass('html_illuminator_view');
        divView.appendTo(divO);

        divView.hide();

        var content = $(html.replace('<!--', '<comment>').replace('-->', '</comment>'));

        content = wrap(content);

        divView.empty();

        content.appendTo(divView);

        divView.show();
    }

    function wrap(content)
    {

        var wrapper = $($('<p>'));
        var open = $($('<span>'));
        var value = $($('<span>'));
        var end = $($('<span>'))
        var textElement = content.get(0).innerHTML;

        var tag = content.get(0).nodeName.toLowerCase();

        var attr = '';
        $.each(content.get(0).attributes, function(i, attrib){
            attr = ' ' + attr + attrib.name + '="'+attrib.value+'" ';
        });


        for(var key in excludedTags)
        {
            var pattern = new RegExp('<' + excludedTags[key] + '.*?>', 'gi');
            textElement = textElement.replace(pattern, ' ');
        }

        var length = textElement.indexOf('<');
        if(length < 0)
        {
            length = textElement.length;
        }
        textElement = textElement.substr(0, length);

        if(tag == 'comment')
        {
            open.text('<!--');
            end.text('-->');

            open.addClass('comment');
            end.addClass('comment');
            wrapper.removeClass('tag_container').addClass('comment_container');
        }
        else
        {
            if($.inArray(tag, excludedTags) == -1)
            {
                open.text('<' + tag + ' ' + attr + '>');
                end.text('</' + tag + '>');
            }
            else
            {
                open.text('<' + tag + ' ' + attr + '/>');
            }

            open.addClass('tag');
            end.addClass('tag');
            wrapper.removeClass('comment_container').addClass('tag_container');
        }

        value.text(textElement);

        wrapper.append(open);
        wrapper.append(value);

        if(content.children().length > 0)
        {
          $.each(content.children(), function(){
          var newWrapper = wrap($(this));
          wrapper.append(newWrapper);
          });
        }

        wrapper.append(end);
        wrapper.click(function(){
            openForEdit($(this));
            return false;
        });
        return wrapper;
    }

    function openForEdit(obj)
    {
        //obj.text(obj.text().replace(/>/g, '>\n'));

        editWindow.textarea.val(obj.text());
        editWindow.css('top', obj.offset().top);
        editWindow.css('left', obj.offset().left);
        editWindow.show();
        editWindow.target_ = obj;
        editWindow.textarea.focus();
    }

    function applyChange(e)
    {
        e.preventDefault();
        editWindow.hide();
        editWindow.target_.hide();

        var container = editWindow.target_;
        var content = $(editWindow.textarea.val().replace('<!--', '<comment>').replace('-->', '</comment>'));
        content = wrap(content);

        container.empty();

        content.children().appendTo(container);
        container.attr('class', content.attr('class'));

        container.show();

        divO.trigger('hi.change_content', [divView.text()]);

    }

    function cancel(e)
    {
        e.preventDefault();
        $(this).parent().parent().parent().hide();
        return false;
    }

    this.getCode = function(){
        return divView.text();
    }

}
