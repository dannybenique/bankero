<link rel="stylesheet" href="libs/fancytree/css/ui.fancytree.css" />
<style type="text/css">
  .ui-menu { width: 180px; font-size: 63%; }
  .ui-menu kbd { float: right; }
  td.alignRight { text-align: right; }
  td.alignCenter { text-align: center; }
  td input[type="input"] { width: 40px; }
</style>
<script type="text/javascript">
  var CLIPBOARD = null;
  $(function() {
    $("#tree")
      .fancytree({
        checkbox: true,
        checkboxAutoHide: true,
        titlesTabbable: true, // Add all node titles to TAB chain
        quicksearch: true, // Jump to nodes when pressing first character
        // source: SOURCE,
        source: { url: "pages/conta/worksheet/ajax-tree-products.json" },
        extensions: ["edit", "dnd5", "table", "gridnav"],
        dnd5: {
          preventVoidMoves: true,
          preventRecursion: true,
          autoExpandMS: 400,
        },
        edit: {
          triggerStart: ["f2", "shift+click", "mac+enter"],
          close: function(event, data) {
            if (data.save && data.isNew) {
              // Quick-enter: add new nodes until we hit [enter] on an empty title
              $("#tree").trigger("nodeCommand", {
                cmd: "addSibling",
              });
            }
          },
        },
        table: {
          indentation: 20,
          nodeColumnIdx: 2,
          checkboxColumnIdx: 0,
        },
        gridnav: {
          autofocusInput: false,
          handleCursorKeys: true,
        },

        lazyLoad: function(event, data) {
          data.result = { url: "/pages/conta/cuentas/ajax-sub2.json" };
        },
        createNode: function(event, data) {
          var node = data.node,
            $tdList = $(node.tr).find(">td");

          // Span the remaining columns if it's a folder.
          // We can do this in createNode instead of renderColumns, because
          // the `isFolder` status is unlikely to change later
          if (node.isFolder()) {
            $tdList
              .eq(2)
              .prop("colspan", 6)
              .nextAll()
              .remove();
          }
        },
        renderColumns: function(event, data) {
          var node = data.node,
            $tdList = $(node.tr).find(">td");

          // (Index #0 is rendered by fancytree by adding the checkbox)
          // Set column #1 info from node data:
          $tdList.eq(1).text(node.getIndexHier());
          // (Index #2 is rendered by fancytree)
          // Set column #3 info from node data:
          $tdList
            .eq(3)
            .find("input")
            .val(node.key);
          $tdList
            .eq(4)
            .find("input")
            .val(node.data.foo);

          // Static markup (more efficiently defined as html row template):
          // $tdList.eq(3).html("<input type='input' value='"  "" + "'>");
          // ...
        },
        modifyChild: function(event, data) {
          data.tree.info(event.type, data);
        },
      })
      .on("nodeCommand", function(event, data) {
        // Custom event handler that is triggered by keydown-handler and context menu:
        var refNode,
          moveMode,
          tree = $.ui.fancytree.getTree(this),
          node = tree.getActiveNode();

        switch (data.cmd) {
          case "addChild":
          case "addSibling":
          case "indent":
          case "moveDown":
          case "moveUp":
          case "outdent":
          case "remove":
          case "rename":
            tree.applyCommand(data.cmd, node);
            break;
          case "cut":
            CLIPBOARD = { mode: data.cmd, data: node };
            break;
          case "copy":
            CLIPBOARD = {
              mode: data.cmd,
              data: node.toDict(true, function(dict, node) {
                delete dict.key;
              }),
            };
            break;
          case "clear":
            CLIPBOARD = null;
            break;
          case "paste":
            if (CLIPBOARD.mode === "cut") {
              // refNode = node.getPrevSibling();
              CLIPBOARD.data.moveTo(node, "child");
              CLIPBOARD.data.setActive();
            } else if (CLIPBOARD.mode === "copy") {
              node.addChildren(
                CLIPBOARD.data
              ).setActive();
            }
            break;
          default:
            alert("Unhandled command: " + data.cmd);
            return;
        }
      })
      .on("keydown", function(e) {
        var cmd = null;

        // console.log(e.type, $.ui.fancytree.eventToString(e));
        switch ($.ui.fancytree.eventToString(e)) { /* mac (meta): cmd+shift+n */
          case "ctrl+shift+n":
          case "meta+shift+n": cmd = "addChild"; break;
          case "ctrl+c":
          case "meta+c": cmd = "copy"; break;
          case "ctrl+v":
          case "meta+v": cmd = "paste"; break;
          case "ctrl+x":
          case "meta+x": cmd = "cut"; break;
          case "ctrl+n":
          case "meta+n": cmd = "addSibling"; break;
          case "del":
          case "meta+backspace": cmd = "remove"; break;
          case "ctrl+up":
          case "ctrl+shift+up": cmd = "moveUp"; break;
          case "ctrl+down":
          case "ctrl+shift+down": cmd = "moveDown"; break;
          case "ctrl+right":
          case "ctrl+shift+right": cmd = "indent"; break;
          case "ctrl+left":
          case "ctrl+shift+left": cmd = "outdent";
        }
        if (cmd) {
          $(this).trigger("nodeCommand", { cmd: cmd });
          return false;
        }
      });

    /**************
     * Tooltips *
    ****************/
    // $("#tree").tooltip({
    //   content: function () {
    //     return $(this).attr("title");
    //   }
    // });

    /***********************************
     * Context menu (https://github.com/mar10/jquery-ui-contextmenu)
    ************************************/
    $("#tree").contextmenu({
      delegate: "span.fancytree-node",
      menu: [
        { cmd: "rename", title: "Edit <kbd>[F2]</kbd>", uiIcon: "ui-icon-pencil",},
        { cmd: "remove", title: "Delete <kbd>[Del]</kbd>", uiIcon: "ui-icon-trash", },
        { title: "----" },
        { cmd: "addSibling", title: "New sibling <kbd>[Ctrl+N]</kbd>", uiIcon: "ui-icon-plus", },
        { cmd: "addChild", title: "New child <kbd>[Ctrl+Shift+N]</kbd>", uiIcon: "ui-icon-arrowreturn-1-e", },
        { title: "----" },
        { cmd: "cut", title: "Cut <kbd>Ctrl+X</kbd>", uiIcon: "ui-icon-scissors", },
        { cmd: "copy", title: "Copy <kbd>Ctrl-C</kbd>", uiIcon: "ui-icon-copy", },
        { cmd: "paste", title: "Paste as child<kbd>Ctrl+V</kbd>", uiIcon: "ui-icon-clipboard", disabled: true, },
      ],
      beforeOpen: function(event, ui) {
        var node = $.ui.fancytree.getNode(ui.target);
        $("#tree").contextmenu("enableEntry", "paste", !!CLIPBOARD );
        node.setActive();
      },
      select: function(event, ui) {
        var that = this;
        // delay the event, so the menu can close and the click event does not interfere with the edit control
        setTimeout(function() { $(that).trigger("nodeCommand", { cmd: ui.cmd }); }, 100);
      },
    });
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-share-alt"></i> WorkSheet</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">WorkSheet</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" id="treeviewBtnADD" class="btn btn-default btn-sm" onclick="return false;"><i class="fa fa-plus"></i></button>
              <button type="button" id="treeviewBtnEDT" class="btn btn-default btn-sm" onclick="return false;"><i class="fa fa-pencil"></i></button>
            </div>
            <button type="button" id="treeviewBtnRLD" class="btn btn-default btn-sm" onclick="javascript:appCuentasResetNodo();"><i class="fa fa-refresh"></i></button>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <div class="box-body">
              <table id="tree">
                <thead>
                  <tr>
                    <th style="width:30px;"></th>
                    <th style="width:50px;">#</th>
                    <th style="width:350px;"></th>
                    <th style="width:50px;">Ed1</th>
                    <th style="width:50px;">Ed2</th>
                    <th style="width:30px;">Rb1</th>
                    <th style="width:30px;">Rb2</th>
                    <th style="width:50px;">Cb</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="alignCenter"></td>
                    <td></td>
                    <td></td>
                    <td><input name="input1" type="input" /></td>
                    <td><input name="input2" type="input" /></td>
                    <td class="alignCenter">
                      <input name="cb1" type="checkbox" />
                    </td>
                    <td class="alignCenter">
                      <input name="cb2" type="checkbox" />
                    </td>
                    <td>
                      <select name="sel1" id="">
                        <option value="a">A</option>
                        <option value="b">B</option>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalCuentas" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalCuentas" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular"><b>CUENTAS</b></h4>
          </div>
          <div class="modal-body" id="divCuentas">
            <div class="box-body">
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>ID</b></span>
                  <input id="txt_ID" name="txt_ID" type="text" class="form-control" style="width:105px;" disabled="disabled"/>
                </div>
              </div>
              <div id="div_Codigo" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>Codigo</b></span>
                  <input id="txt_Codigo" name="txt_Codigo" type="text" class="form-control" maxlength="10" style="width:205px;"/>
                </div>
              </div>
              <div id="div_Nombre" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>Nombre</b></span>
                  <input id="txt_Nombre" name="txt_Nombre" type="text" class="form-control"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>Agregado a...</b></span>
                  <input id="txt_Parent" name="txt_Parent" type="text" class="form-control" disabled="disabled"/>
                  <input id="hid_ParentID" type="hidden" value=""/>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" id="btn_modalCerrar" class="btn btn-default pull-left btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modalCuentasInsert" class="btn btn-primary btn-sm" onclick="javascript:modalCuentasInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btn_modalCuentasUpdate" class="btn btn-info btn-sm" onclick="javascript:modalCuentasUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="//cdn.jsdelivr.net/npm/ui-contextmenu/jquery.ui-contextmenu.min.js"></script>
<script src="libs/fancytree/js/jquery.fancytree.js"></script>
<script src="libs/fancytree/js/jquery.fancytree.dnd5.js"></script>
<script src="libs/fancytree/js/jquery.fancytree.edit.js"></script>
<script src="libs/fancytree/js/jquery.fancytree.gridnav.js"></script>
<script src="libs/fancytree/js/jquery.fancytree.table.js"></script>
<script src="pages/conta/worksheet/worksheet.js"></script>
<script>
  $(document).ready(function(){
    appCuentasRootGetAll();
  });
</script>
