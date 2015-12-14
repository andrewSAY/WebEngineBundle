var tree_view_group_constructor = function (elementId, formDialog) {
    if (typeof elementId != 'string') {
        throw  new Error(elementId + ' is not string');
    }
    if (elementId.indexOf('#') != 0) {
        throw  new Error(elementId + ' is not identificator of DOM-element ');
    }

    var tObject = {
        id: elementId,
        element: null,
        rNode: null,
        shiftPress: false,
        ctrlPress: false,
        multiMoveInProcess: false,
        init: function () {
            var self = this;
            $(document).keydown(function (e) {
                if (e.which == 16) {
                    self.shiftPress = true;
                }
                if (e.which == 17) {
                    self.ctrlPress = true;
                }
            });
            $(document).keyup(function (e) {
                if (e.which == 16) {
                    self.shiftPress = false;
                }
                if (e.which == 17) {
                    self.ctrlPress = false
                }
            });

        },
        applyTree: function () {
            var self = this;
            if (self.element == null) {
                self.element = $(self.id);
            }
            self.element.tree({
                dragAndDrop: true,
                useContextMenu: false,
                onCreateLi: function (node, $li) {
                    $li.dblclick(function(){
                        window.location = node.link;
                    });
                },
                onCanMove: function (node) {
                    return true;
                },
                onCanMoveTo: function (movedNode, targetNode, position) {

                    if (position == 'before') {
                        return false;
                    }
                    return position;
                }
            });
            self.element.bind(
                'tree.click',
                function (event) {
                    if (self.shiftPress && self.element.tree('getSelectedNodes').length > 0) {
                        event.preventDefault();
                        var node = self.element.tree('getSelectedNodes')[0];
                        var _contiune = true;
                        var directionUp = false;

                        if (node.getLevel() < event.node.getLevel()) {
                            var parent_ = event.node.parent;
                            while (parent_.getLevel() != node.getLevel()) {
                                parent_ = parent_.parent;
                            }
                            event.node = parent_;
                        }

                        if ($(event.node.element).index() < $(node.element).index()) {
                            directionUp = true;
                        }
                        while (_contiune) {
                            if (directionUp) {
                                node = node.getPreviousSibling();
                            }
                            else {
                                node = node.getNextSibling();
                            }
                            self.element.tree('addToSelection', node);
                            node.indexP = $(node.element).index();
                            if (node.id == event.node.id) {
                                _contiune = false;
                            }
                        }
                        return;
                    }

                    if (self.ctrlPress) {
                        event.preventDefault();
                        if (!self.element.tree('isNodeSelected', event.node)) {
                            if (self.element.tree('getSelectedNodes').length > 0) {
                                if (event.node.parent.id != self.element.tree('getSelectedNodes')[0].parent.id) {
                                    var selecteedNodes = self.element.tree('getSelectedNodes');
                                    for (
                                        var i = 0; i < selecteedNodes.length; i++) {
                                        self.element.tree('removeFromSelection', selecteedNodes[i]);
                                    }
                                }
                            }
                            self.element.tree('addToSelection', event.node);
                            event.node.indexP = $(event.node.element).index();
                        }
                        else {
                            self.element.tree('removeFromSelection', event.node);
                        }
                        return;
                    }
                    var selecteedNodes = self.element.tree('getSelectedNodes');
                    for (
                        var i = 0; i < selecteedNodes.length; i++) {
                        self.element.tree('removeFromSelection', selecteedNodes[i]);
                    }
                    self.element.tree('toggle', event.node);

                }
            );
            self.element.bind(
                'tree.move',
                function (event) {
                    if (self.shiftPress && !self.multiMoveInProcess) {
                        event.preventDefault();
                        self.multiMove(event.move_info.target_node, event.move_info.position);
                        return false;
                    }
                    var collection = new Array();
                    collection = self.compileMoveCollection(event.move_info.moved_node, event.move_info.target_node, event.move_info.position, collection);
                    self.moveToServer(collection);
                }
            );
            self.element.bind(
                'tree.select',
                function (event) {
                    event.node.indexP = $(event.node.element).index();
                });
        },

        multiMove: function (targetNode, positionCase) {
            var self = this;
            self.multiMoveInProcess = true;

            var nodes = self.element.tree('getSelectedNodes');
            nodes.sort(function (x, y) {
                return (parseInt(x.indexP) - parseInt(y.indexP));
            });
            var collection = new Array();
            for (
                var i = 0; i < nodes.length; i++) {
                if (targetNode.getLevel() >= nodes[i].getLevel()) {
                    var continue_ = true;
                    var toNext = false
                    var parent_ = targetNode;
                    while (continue_) {
                        if (nodes[i].id == parent_.id) {
                            toNext = true;
                            continue_ = false;
                        }
                        parent_ = parent_.parent;
                        if (parent_.id == undefined) {
                            continue_ = false;
                        }
                    }

                    if (toNext) {
                        break;
                    }
                }

                if (i == 0) {
                    self.element.tree('moveNode', nodes[i], targetNode, positionCase);
                    nodes[i].indexP = $(nodes[i].element).index();
                    collection = self.compileMoveCollection(nodes[i], targetNode, positionCase, collection);
                    continue;
                }
                else {
                    self.element.tree('moveNode', nodes[i], nodes[i - 1], 'after');
                    nodes[i].indexP = $(nodes[i].element).index();
                    collection = self.compileMoveCollection(nodes[i], nodes[i - 1], 'after', collection);
                }
            }

            self.multiMoveInProcess = false;
            self.moveToServer(collection);
        },
        compileMoveCollection: function (node, target_node, position, collection) {
            if (typeof collection != 'object') {
                throw new Error('Variable collection is not object! [' + (typeof collection) + ']');
            }
            var movedData = new Object();
            movedData.position = 0;
            movedData.owner = target_node.id;
            movedData.node = node.id;

            switch (position) {
                case 'inside':
                    movedData.position = 1;
                    break;
                case 'after':
                    var i = $(target_node.element).index();
                    if (target_node.parent.id == node.parent.id) {
                        if (i < $(node.element).index()) {
                            i = i + 1;
                        }
                    }
                    else {
                        i = i + 1;
                    }
                    movedData.position = i + 1;
                    if (target_node.parent.id == undefined) {
                        movedData.owner = 0;
                    }
                    else {
                        movedData.owner = target_node.parent.id;
                    }
                    break;
                case 'before':
                    alert();
                    break;
            }
            collection.push(movedData);
            return collection;
        },
        moveToServer: function (collection) {
            var form = $('.record_actions li form:eq(0)');
            form.find('input[name="form[moves_data]"]').val(JSON.stringify(collection));
            formDialog.postData(form.attr('action'), form.serializeArray(), null, function (data) {
                formDialog.errorDialog.open(data.view);
            });
        },
        loadDataOnTree: function (data) {
            var self = this;
            self.element.tree('loadData', JSON.parse(data).children);
        },
        appendNodeToRoot: function (data) {
            var self = this;
            self.element.tree('appendNode', JSON.parse(data));
        },
        appendNodeToNode: function (data) {
            var self = this;
            self.element.tree('appendNode', JSON.parse(data), self.rNode);
        },
        updateNode: function (data) {
            var self = this;
            data = JSON.parse(data);
            var newData = new Object();
            newData.label = data.label;
            newData.active = data.active;
            newData.users = data.users;
            self.element.tree('updateNode', self.rNode, newData);
        },
        removeRNode: function () {
            var self = this;
            self.element.tree('removeNode', self.rNode);
        }

    }

    tObject.init();
    return tObject;
}


