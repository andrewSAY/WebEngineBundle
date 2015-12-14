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

        divView = $($('<div contenteditable="true">')).addClass('html_illuminator_view');
        divView.appendTo(divO);
        divView.on('paste', onPaste);
        divView.get(0).onkeypress = function(e){
            e.cancelable = false;
            alert(print_object(e));
        };

        divView.hide();
        var htmlStrings = getHtmlStrings(html);
        for(var i in htmlStrings)
        {
            var element = wrap(htmlStrings[i]);
            element.appendTo(divView);
        }
        divView.children().each(function(){
           paint($(this).children());
        });
        divView.show();

        /*var content = $(html.replace('<!--', '<comment>').replace('-->', '</comment>'));

        content = wrap(content);

        divView.empty();

        content.appendTo(divView);

        divView.show();*/
    }

    function getHtmlStrings(text)
    {
        var arr = text.split(/\r\n|\r|\n/g);
        //alert(print_object(arr));
        return arr;
    }

    function wrap(content)
    {
        var container = $($('<p>')).addClass('tag_container');
        container.keypress(function(e){

        });
        for(var i in content)
        {
            var charContainer = createCharWrapper(content[i]);
            charContainer.appendTo(container);
            charContainer.on('keypress', function(e){
                var container = createCharWrapper(String.fromCharCode(e.charCode));
                paint(container.parent().children());
            });
        }

        return container;
    }

    function paint(collection)
    {
        var tagOpened = false;
        var tagClosed = false;
        for(var i = 0; i < collection.length; i++)
        {

           var currentNode = collection.eq(i);
           var currentValue = currentNode.text();
           if(currentValue == '<')
           {
              tagOpened = true;
              tagClosed = false;
           }
           if(currentValue == '>')
           {
               tagOpened = false;
               tagClosed = true;
           }
           if(tagOpened && !tagClosed)
           {
             currentNode.addClass('tag');
           }
        }
    }

    function onPaste(e)
    {
        divView.hide();
        var html = e.originalEvent.clipboardData.getData('Text');
        e.preventDefault();
        var htmlStrings = getHtmlStrings(html);
        for(var i in htmlStrings)
        {
            var element = wrap(htmlStrings[i]);
            element.appendTo(divView);
        }
        paint(divView.children());
        divView.show();
        return false;
    }

    function paintCharInContext(contenxt, index)
    {
        var value = contenxt[index];
        var class_ = '';
        if(value == '<' || value == '>')
        {
            class_ = 'tag';
        }

        return class_;
    }

    function createCharWrapper(value)
    {
        //var value = String.fromCharCode(charCode);
        var charWrraper = $($('<span>')).text(value);


        return charWrraper;
    }

    function openForEdit(obj)
    {
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
