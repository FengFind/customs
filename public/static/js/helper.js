/**
 * Created by Administrator on 2017/10/17.
 */
//验证搜索框是否为空
function hasString(obj) {
    obj.value == undefined ? obj = obj[0] : "";
    return obj.value.replace(/(^\s*)|(\s*$)/g,"").length
}

//数据表格某列内容对齐方式设置
function setAlign(listarr,alignType) {
    var html = '';
    var alignType = alignType || 'center';
    $(listarr).each(function(index,val) {
        html +='.datas .layui-table-view .layui-table td[data-field="'+val+'"] {text-align: '+alignType+';}';
    });
    $(document.body).append( '<style>'+html+'</style>');
}

//请求时的过渡动画，防止用户重复点击
function reqTransation(bool) {
    bool ? layer.load(0,{shade:0.3}) : layer.closeAll('loading');
}
